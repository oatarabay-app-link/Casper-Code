using Casper.API.Models.Casper.API.Models;
using Casper.API.Models.CasperVpnDbContext;
using Casper.API.Models.Requests;
using Casper.API.Models;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using Casper.API.Services.Interfaces;

namespace Casper.API.DateSeeder
{
    public class DataSeeder
    {
        public static async Task SeedDefaultData(IServiceProvider serviceProvider)
        {
            using var scope = serviceProvider.CreateScope();
            var serviceProviderScoped = scope.ServiceProvider;

            try
            {
                // Seed default permissions first
                await SeedDefaultPermissions(serviceProviderScoped);

                // Seed default roles
                await SeedDefaultRoles(serviceProviderScoped);

                // Seed default admin user
                await SeedDefaultAdminUser(serviceProviderScoped);

                // Seed default client user
                await SeedDefaultClientUser(serviceProviderScoped);

                // Assign permissions to Admin role
                await AssignAdminPermissions(serviceProviderScoped);

            }
            catch (Exception ex)
            {
                Console.WriteLine($"❌ Error seeding default data: {ex.Message}");
            }
        }

        private static async Task SeedDefaultPermissions(IServiceProvider serviceProvider)
        {
            var roleService = serviceProvider.GetRequiredService<IRoleService>();

            var result = await roleService.SeedPermissionsAsync();
            if (result.IsSuccess)
            {
                Console.WriteLine("✅ Default permissions seeded successfully!");
            }
            else
            {
                Console.WriteLine($"❌ Failed to seed permissions: {result.ErrorMessage}");
            }
        }

        private static async Task SeedDefaultRoles(IServiceProvider serviceProvider)
        {
            var roleManager = serviceProvider.GetRequiredService<RoleManager<IdentityRole>>();

            var roles = new[] { "Admin", "User" };

            foreach (var role in roles)
            {
                if (!await roleManager.RoleExistsAsync(role))
                {
                    await roleManager.CreateAsync(new IdentityRole(role));
                    Console.WriteLine($"✅ Created role: {role}");
                }
            }
        }

        private static async Task SeedDefaultAdminUser(IServiceProvider serviceProvider)
        {
            var userManager = serviceProvider.GetRequiredService<UserManager<Users>>();
            var authService = serviceProvider.GetRequiredService<IAuthService>();

            // Requested admin credentials
            var adminEmail = "admin@caspervpn.com"; // also used as username now
            // Must satisfy: >=8 chars, at least 1 upper, 1 lower, 1 digit, 1 special from @$!%*?&
            var desiredPassword = "Admin@123456"; // conforms to validator rules
            // test
            // Always remove existing admin user if it exists
            var existingAdminUser = await userManager.FindByEmailAsync(adminEmail);
            if (existingAdminUser != null)
            {
                // Clean up dependent data that may block deletion (e.g., refresh tokens)
                try
                {
                    var db = serviceProvider.GetRequiredService<CasperVpnDbContext>();
                    var tokens = await db.RefreshTokens
                        .Where(t => t.UserId == existingAdminUser.Id)
                        .ToListAsync();
                    if (tokens.Count > 0)
                    {
                        db.RefreshTokens.RemoveRange(tokens);
                        await db.SaveChangesAsync();
                        Console.WriteLine($"🧹 Removed {tokens.Count} existing refresh token(s) for admin user.");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"⚠️  Failed cleaning dependent data before deleting admin: {ex.Message}");
                }

                var deleteResult = await userManager.DeleteAsync(existingAdminUser);
                if (deleteResult.Succeeded)
                {
                    Console.WriteLine("🗑️  Existing admin user deleted successfully!");
                }
                else
                {
                    Console.WriteLine($"⚠️  Failed to delete existing admin user: {string.Join(", ", deleteResult.Errors.Select(e => e.Description))}");
                }
            }

            // Always create a fresh admin user
            var createUserRequest = new CreateUserRequest
            {
                UserName = adminEmail, // username set to email as requested
                Email = adminEmail,
                Password = desiredPassword,
                PhoneNumber = "+1234567890",
                Role = "Admin"
            };

            var result = await authService.CreateUserAsync(createUserRequest);

            if (result.IsSuccess)
            {
                Console.WriteLine("✅ Fresh admin user created successfully!");
            }
            else
            {
                Console.WriteLine($"❌ Failed to create admin user: {result.ErrorMessage}");
            }
        }

        private static async Task SeedDefaultClientUser(IServiceProvider serviceProvider)
        {
            var userManager = serviceProvider.GetRequiredService<UserManager<Users>>();
            var authService = serviceProvider.GetRequiredService<IAuthService>();

            const string clientEmail = "user@caspervpn.com";
            const string desiredPassword = "User@123456";

            var existingClientUser = await userManager.FindByEmailAsync(clientEmail);
            if (existingClientUser != null)
            {
                try
                {
                    var db = serviceProvider.GetRequiredService<CasperVpnDbContext>();
                    var tokens = await db.RefreshTokens
                        .Where(t => t.UserId == existingClientUser.Id)
                        .ToListAsync();
                    if (tokens.Count > 0)
                    {
                        db.RefreshTokens.RemoveRange(tokens);
                        await db.SaveChangesAsync();
                        Console.WriteLine($"🧹 Removed {tokens.Count} existing refresh token(s) for client user.");
                    }
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"⚠️  Failed cleaning dependent data before deleting client user: {ex.Message}");
                }

                var deleteResult = await userManager.DeleteAsync(existingClientUser);
                if (deleteResult.Succeeded)
                {
                    Console.WriteLine("🗑️  Existing client user deleted successfully!");
                }
                else
                {
                    Console.WriteLine($"⚠️  Failed to delete existing client user: {string.Join(", ", deleteResult.Errors.Select(e => e.Description))}");
                }
            }

            var createUserRequest = new CreateUserRequest
            {
                UserName = clientEmail,
                Email = clientEmail,
                Password = desiredPassword,
                PhoneNumber = "+1234567891",
                Role = "User"
            };

            var result = await authService.CreateUserAsync(createUserRequest);

            if (result.IsSuccess)
            {
                Console.WriteLine("✅ Fresh client user created successfully!");
            }
            else
            {
                Console.WriteLine($"❌ Failed to create client user: {result.ErrorMessage}");
            }
        }

        private static async Task AssignAdminPermissions(IServiceProvider serviceProvider)
        {
            var roleService = serviceProvider.GetRequiredService<IRoleService>();
            var roleManager = serviceProvider.GetRequiredService<RoleManager<IdentityRole>>();

            var adminRole = await roleManager.FindByNameAsync("Admin");
            if (adminRole != null)
            {
                // Get all permissions to assign to Admin role
                var allPermissionsResult = await roleService.GetAllPermissionsAsync();
                if (allPermissionsResult.IsSuccess)
                {
                    var permissionNames = (allPermissionsResult.Data ?? Enumerable.Empty<Permission>())
                        .Select(p => p.Name)
                        .ToList();

                    var result = await roleService.AssignPermissionsToRoleAsync(adminRole.Id, permissionNames);
                    if (result.IsSuccess)
                    {
                        Console.WriteLine($"✅ All permissions assigned to Admin role successfully! ({permissionNames.Count} permissions)");
                    }
                    else
                    {
                        Console.WriteLine($"❌ Failed to assign permissions to Admin role: {result.ErrorMessage}");
                    }
                }
            }
        }

        //public static async Task SeedDefaultServers(IServiceProvider serviceProvider)
        //{
        //    var dbContext = serviceProvider.GetRequiredService<CasperVpnDbContext>();

        //    if (!dbContext.Servers.Any())
        //    {
        //        var defaultServers = new List<Server>
        //    {
        //        new Server
        //        {
        //            ServerName = "US-East-1",
        //            Location = "New York",
        //                Server_Status = Enums.ServerStatus.Online,
        //            ConnectedUsers = 1247,
        //            Load = 65,
        //            IPAddress = "198.51.100.1"
        //        },
        //        new Server
        //        {
        //            ServerName = "US-West-1",
        //            Location = "Los Angeles",
        //            Server_Status = Enums.ServerStatus.Online,
        //            ConnectedUsers = 892,
        //            Load = 45,
        //            IPAddress = "198.51.100.2"
        //        },
        //        new Server
        //        {
        //            ServerName = "EU-Central-1",
        //            Location = "Frankfurt",
        //              Server_Status = Enums.ServerStatus.Maintenance,
        //            ConnectedUsers = 0,
        //            Load = 0,
        //            IPAddress = "198.51.100.3"
        //        },
        //        new Server
        //        {
        //            ServerName = "Asia-East-1",
        //            Location = "Tokyo",
        //    Server_Status = Enums.ServerStatus.Online,
        //            ConnectedUsers = 634,
        //            Load = 78,
        //            IPAddress = "198.51.100.4"
        //        },

        //    };

        //        await dbContext.Servers.AddRangeAsync(defaultServers);
        //        await dbContext.SaveChangesAsync();

        //        Console.WriteLine("✅ Default servers created successfully!");
        //    }
        //    else
        //    {
        //        Console.WriteLine("ℹ️  Servers already exist.");
        //    }
        //}
    }
}