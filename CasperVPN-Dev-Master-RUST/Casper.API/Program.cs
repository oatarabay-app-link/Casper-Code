using System.Text;
using FluentValidation;
using Microsoft.AspNetCore.Authentication.JwtBearer;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using Microsoft.IdentityModel.Tokens;
using Microsoft.AspNetCore.HttpOverrides;
using Microsoft.OpenApi.Models;
using Casper.API.DateSeeder;
using Casper.API.Interfaces;
using Casper.API.Models;
using Casper.API.Models.CasperVpnDbContext;
using Casper.API.Services;

namespace Casper.API
{
    public class Program
    {
        public static async Task Main(string[] args)
        {
            var builder = WebApplication.CreateBuilder(args);

            // ===========================================
            // 1. CONFIGURE KESTREL WEB SERVER
            // ===========================================
            ConfigureKestrel(builder);
            //commented for behind the nginx
            // ConfigureHttpsRedirection(builder);

            // ===========================================
            // 2. CONFIGURE DATABASE & ENTITY FRAMEWORK
            // ===========================================
            ConfigureDatabase(builder);

            // ===========================================
            // 3. CONFIGURE IDENTITY & AUTHENTICATION
            // ===========================================
            ConfigureIdentity(builder);
            ConfigureJwtAuthentication(builder);

            // ===========================================
            // 4. CONFIGURE APPLICATION SERVICES
            // ===========================================
            ConfigureApplicationServices(builder);

            // ===========================================
            // 5. CONFIGURE API DOCUMENTATION (SWAGGER)
            // ===========================================
            ConfigureSwagger(builder);

            // ===========================================
            // 6. CONFIGURE AUTHORIZATION & CORS
            // ===========================================
            ConfigureAuthorization(builder);
            ConfigureCors(builder);
            ConfigureForwardedHeaders(builder);

            // ===========================================
            // 7. BUILD APPLICATION
            // ===========================================
            var app = builder.Build();

            // ===========================================
            // 8. CONFIGURE MIDDLEWARE PIPELINE
            // ===========================================
            await ConfigureMiddleware(app);

            // ===========================================
            // 9. RUN APPLICATION
            // ===========================================
            app.Run();
        }

        #region Configuration Methods

        /// <summary>
        /// Configure Kestrel web server with HTTPS support
        /// </summary>
        private static void ConfigureKestrel(WebApplicationBuilder builder)
        {
            // Configure Kestrel once; avoid nested UseKestrel which attempts to re-register services
            builder.WebHost.ConfigureKestrel(o =>
            {
                o.ListenLocalhost(5001); // HTTP
                o.ListenLocalhost(5000, lo => lo.UseHttps()); // HTTPS (dev cert)
            });
        }

        /// <summary>
        /// Explicitly configure HTTPS redirection port.
        /// This prevents InvalidOperationException when multiple HTTPS addresses are configured
        /// (e.g., both in code and appsettings Kestrel:Endpoints).
        /// </summary>
        private static void ConfigureHttpsRedirection(WebApplicationBuilder builder)
        {
            // Do NOT force a specific HTTPS port; let the host/proxy dictate it.
            // This avoids redirects to :5000 when running behind a reverse proxy.
            builder.Services.Configure<Microsoft.AspNetCore.HttpsPolicy.HttpsRedirectionOptions>(options =>
            {
                options.HttpsPort = null; // use default/feature if available
            });
        }

        /// <summary>
        /// Configure database connection and Entity Framework
        /// </summary>
        private static void ConfigureDatabase(WebApplicationBuilder builder)
        {
            var connectionString = builder.Configuration.GetConnectionString("Dev");

            builder.Services.AddDbContext<CasperVpnDbContext>(options =>
                options.UseMySql(connectionString, ServerVersion.AutoDetect(connectionString)));
        }

        /// <summary>
        /// Configure ASP.NET Core Identity
        /// </summary>
        private static void ConfigureIdentity(WebApplicationBuilder builder)
        {
            builder.Services.AddIdentity<Users, IdentityRole>()
                .AddEntityFrameworkStores<CasperVpnDbContext>()
                .AddDefaultTokenProviders();
        }

        /// <summary>
        /// Configure JWT Authentication
        /// </summary>
        private static void ConfigureJwtAuthentication(WebApplicationBuilder builder)
        {
            var jwtSettings = builder.Configuration.GetSection("Jwt");
            var key = jwtSettings["Key"];

            if (string.IsNullOrEmpty(key) || key.Length < 32)
            {
                throw new InvalidOperationException("JWT Key in appsettings is less than 32 characters or empty");
            }

            var keyBytes = Encoding.UTF8.GetBytes(key);

            builder.Services.AddAuthentication(options =>
            {
                options.DefaultAuthenticateScheme = JwtBearerDefaults.AuthenticationScheme;
                options.DefaultChallengeScheme = JwtBearerDefaults.AuthenticationScheme;
            })
            .AddJwtBearer(options =>
            {
                options.TokenValidationParameters = new TokenValidationParameters
                {
                    ValidateIssuerSigningKey = true,
                    IssuerSigningKey = new SymmetricSecurityKey(keyBytes),
                    ValidateIssuer = true,
                    ValidIssuer = jwtSettings["Issuer"],
                    ValidateAudience = true,
                    ValidAudience = jwtSettings["Audience"],
                    ValidateLifetime = true,
                    ClockSkew = TimeSpan.Zero
                };

                options.Events = new JwtBearerEvents
                {
                    OnAuthenticationFailed = context =>
                    {
                        Console.WriteLine($"Authentication failed: {context.Exception.Message}");
                        if (context.Exception is SecurityTokenInvalidSignatureException sigEx)
                        {
                            Console.WriteLine($"Signature validation failed: {sigEx.Message}");
                        }
                        return Task.CompletedTask;
                    },
                    OnTokenValidated = context =>
                    {
                        Console.WriteLine("Token validated successfully");
                        return Task.CompletedTask;
                    }
                };
            });
        }

        /// <summary>
        /// Configure application services and dependency injection
        /// </summary>
        private static void ConfigureApplicationServices(WebApplicationBuilder builder)
        {
            // Register custom services
            builder.Services.AddScoped<IAuthService, AuthService>();
            builder.Services.AddScoped<IServerService, ServerService>();
            builder.Services.AddScoped<IRoleService, RoleService>();
            // Add FluentValidation
            builder.Services.AddValidatorsFromAssemblyContaining<Program>();

            // Add MVC services
            builder.Services.AddControllers();
            builder.Services.AddEndpointsApiExplorer();
        }

        /// <summary>
        /// Configure Swagger/OpenAPI documentation
        /// </summary>
        private static void ConfigureSwagger(WebApplicationBuilder builder)
        {
            builder.Services.AddSwaggerGen(options =>
            {
                options.SwaggerDoc("v1", new OpenApiInfo
                {
                    Title = "Casper VPN API",
                    Version = "v1",
                    Description = "A comprehensive API for Casper VPN management"
                });

                // Add JWT Bearer authentication to Swagger
                options.AddSecurityDefinition("Bearer", new OpenApiSecurityScheme
                {
                    Name = "Authorization",
                    Type = SecuritySchemeType.ApiKey,
                    Scheme = "Bearer",
                    BearerFormat = "JWT",
                    In = ParameterLocation.Header,
                    Description = "JWT Authorization header using the Bearer scheme. Enter 'Bearer' [space] and then your token."
                });

                options.AddSecurityRequirement(new OpenApiSecurityRequirement
                {
                    {
                        new OpenApiSecurityScheme
                        {
                            Reference = new OpenApiReference
                            {
                                Type = ReferenceType.SecurityScheme,
                                Id = "Bearer"
                            }
                        },
                        Array.Empty<string>()
                    }
                });
            });
        }

        /// <summary>
        /// Configure authorization policies
        /// </summary>
        private static void ConfigureAuthorization(WebApplicationBuilder builder)
        {
            builder.Services.AddAuthorization(options =>
            {
                options.AddPolicy("Admin", policy => policy.RequireRole("Admin"));
                options.AddPolicy("User", policy => policy.RequireRole("User", "Admin"));
            });
        }

        /// <summary>
        /// Configure CORS (Cross-Origin Resource Sharing)
        /// </summary>
        private static void ConfigureCors(WebApplicationBuilder builder)
        {
            builder.Services.AddCors(options =>
            {
                options.AddPolicy("AllowFrontend", policy =>
                {
                    policy.WithOrigins(
                        "http://localhost:48734",
                        "https://casper.m7slabs.com",
                        "https://www.casper.m7slabs.com",
                        "https://localhost:48734",
                        "http://localhost:5173",
                        "https://localhost:5173"
                    )
                    .AllowAnyHeader()
                    .AllowAnyMethod()
                    .AllowCredentials();
                });
            });
        }

        /// <summary>
        /// Configure Forwarded Headers so the app respects X-Forwarded-Proto/For/Host from Nginx.
        /// This prevents UseHttpsRedirection from forcing local HTTPS ports when behind a reverse proxy.
        /// </summary>
        private static void ConfigureForwardedHeaders(WebApplicationBuilder builder)
        {
            builder.Services.Configure<ForwardedHeadersOptions>(options =>
            {
                options.ForwardedHeaders = ForwardedHeaders.XForwardedFor | ForwardedHeaders.XForwardedProto | ForwardedHeaders.XForwardedHost;
                options.KnownProxies.Add(System.Net.IPAddress.Loopback);
                options.KnownProxies.Add(System.Net.IPAddress.IPv6Loopback);
            });
        }

        /// <summary>
        /// Configure middleware pipeline and seed data
        /// </summary>
        private static async Task ConfigureMiddleware(WebApplication app)
        {
            // Seed default data
            await DataSeeder.SeedDefaultData(app.Services);

            // Configure middleware pipeline
            app.UseForwardedHeaders();
            app.UseCors("AllowFrontend");

            // Development-specific middleware
            if (app.Environment.IsDevelopment())
            {
                app.UseSwagger();
                app.UseSwaggerUI(c =>
                {
                    c.SwaggerEndpoint("/swagger/v1/swagger.json", "Casper VPN API v1");
                    c.RoutePrefix = "swagger";
                });
            }

            // Security middleware
            app.UseHttpsRedirection();
            app.UseAuthentication();
            app.UseAuthorization();

            // API routing
            app.MapControllers();
        }

        #endregion
    }
}