using System.Security.Claims;
using System.Text;
using System.Text.Json;
using Casper.API.DateSeeder;
using Casper.API.Models;
using Casper.API.Models.CasperVpnDbContext;
using Casper.API.Services;
using Casper.API.Services.Interfaces;
using FluentValidation;
using Microsoft.AspNetCore.Authentication.JwtBearer;
using Microsoft.AspNetCore.HttpOverrides;
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.Versioning;
using Microsoft.EntityFrameworkCore;
using Microsoft.IdentityModel.Tokens;
using Microsoft.OpenApi.Models;
using Swashbuckle.AspNetCore.Annotations;

namespace Casper.API
{
    public class Program
    {
        public static async Task Main(string[] args)
        {
            try
            {
                Console.WriteLine("Starting Casper VPN API...");

                var builder = WebApplication.CreateBuilder(args);

                // Add configuration
                builder.Configuration.AddJsonFile("appsettings.json", optional: false, reloadOnChange: true);
                builder.Configuration.AddEnvironmentVariables();

                // 1. Configure Database
                ConfigureDatabase(builder);

                // 2. Configure Identity
                ConfigureIdentity(builder);

                // 3. Configure Authentication (JWT Only - Manual OAuth)
                ConfigureAuthentication(builder);

                // 4. Configure Application Services
                ConfigureApplicationServices(builder);

                // 5. Configure Swagger
                ConfigureSwagger(builder);

                // 6. Configure CORS & Authorization
                ConfigureCors(builder);
                ConfigureAuthorization(builder);

                var app = builder.Build();

                // 7. Configure Middleware Pipeline
                await ConfigureMiddleware(app);

                Console.WriteLine("Casper VPN API started successfully.");
                app.Run();
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Application failed to start: {ex}");
                throw;
            }
        }

        private static void ConfigureDatabase(WebApplicationBuilder builder)
        {
            var connectionString = builder.Configuration.GetConnectionString("Dev");

            if (string.IsNullOrEmpty(connectionString))
            {
                throw new InvalidOperationException("Database connection string 'LocalMySql' is not configured.");
            }

            builder.Services.AddDbContext<CasperVpnDbContext>(options =>
            {
                options.UseMySql(connectionString, ServerVersion.AutoDetect(connectionString));
                options.EnableSensitiveDataLogging(builder.Environment.IsDevelopment());
            });

            Console.WriteLine("Database configured successfully.");
        }






        private static void ConfigureIdentity(WebApplicationBuilder builder)
        {
            builder.Services.AddIdentity<Users, IdentityRole>(options =>
            {
                // Password settings
                options.Password.RequireDigit = true;
                options.Password.RequiredLength = 6;
                options.Password.RequireNonAlphanumeric = false;
                options.Password.RequireUppercase = true;
                options.Password.RequireLowercase = true;

                // Lockout settings
                options.Lockout.DefaultLockoutTimeSpan = TimeSpan.FromMinutes(30);
                options.Lockout.MaxFailedAccessAttempts = 5;
                options.Lockout.AllowedForNewUsers = true;

                // User settings
                options.User.AllowedUserNameCharacters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-._@+";
                options.User.RequireUniqueEmail = true;

                // SignIn settings
                options.SignIn.RequireConfirmedEmail = false;
                options.SignIn.RequireConfirmedPhoneNumber = false;
                options.SignIn.RequireConfirmedAccount = false;
            })
            .AddEntityFrameworkStores<CasperVpnDbContext>()
            .AddDefaultTokenProviders();

            // Configure application cookie
            builder.Services.ConfigureApplicationCookie(options =>
            {
                options.Cookie.HttpOnly = true;
                options.Cookie.SameSite = SameSiteMode.None;
                options.Cookie.SecurePolicy = CookieSecurePolicy.Always;
                options.LoginPath = "/api/v1/auth/login";
                options.LogoutPath = "/api/v1/auth/logout";
                options.AccessDeniedPath = "/api/v1/auth/access-denied";
                options.SlidingExpiration = true;
                options.ExpireTimeSpan = TimeSpan.FromDays(7);
            });

            Console.WriteLine("Identity configured successfully.");
        }

        private static void ConfigureAuthentication(WebApplicationBuilder builder)
        {
            var jwtSettings = builder.Configuration.GetSection("Jwt");
            var key = jwtSettings["Key"];

            if (string.IsNullOrEmpty(key) || key.Length < 32)
            {
                throw new InvalidOperationException("JWT Key must be at least 32 characters long and configured in appsettings.json.");
            }

            builder.Services.AddAuthentication(options =>
            {
                options.DefaultAuthenticateScheme = JwtBearerDefaults.AuthenticationScheme;
                options.DefaultChallengeScheme = JwtBearerDefaults.AuthenticationScheme;
                options.DefaultScheme = JwtBearerDefaults.AuthenticationScheme;
            })
            .AddJwtBearer(options =>
            {
                options.TokenValidationParameters = new TokenValidationParameters
                {
                    ValidateIssuerSigningKey = true,
                    IssuerSigningKey = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(key)),
                    ValidateIssuer = true,
                    ValidIssuer = jwtSettings["Issuer"],
                    ValidateAudience = true,
                    ValidAudience = jwtSettings["Audience"],
                    ValidateLifetime = true,
                    ClockSkew = TimeSpan.Zero,
                    NameClaimType = ClaimTypes.Name,
                    RoleClaimType = ClaimTypes.Role
                };

                options.Events = new JwtBearerEvents
                {
                    OnMessageReceived = context =>
                    {
                        // Allow token in query string for OAuth callbacks
                        if (context.Request.Path.StartsWithSegments("/api/v1/auth/oauth/google/callback"))
                        {
                            var token = context.Request.Query["access_token"];
                            if (!string.IsNullOrEmpty(token))
                            {
                                context.Token = token;
                            }
                        }
                        return Task.CompletedTask;
                    },
                    OnAuthenticationFailed = context =>
                    {
                        Console.WriteLine($"JWT Authentication failed: {context.Exception.Message}");
                        return Task.CompletedTask;
                    },
                    OnTokenValidated = context =>
                    {
                        Console.WriteLine("JWT Token validated successfully");
                        return Task.CompletedTask;
                    }
                };

                if (builder.Environment.IsDevelopment())
                {
                    options.IncludeErrorDetails = true;
                }
            });

            Console.WriteLine("Authentication configured successfully (JWT only - manual OAuth).");
        }

        private static void ConfigureApplicationServices(WebApplicationBuilder builder)
        {

            // Add global lowercase URL configuration
            builder.Services.Configure<RouteOptions>(options =>
            {
                options.LowercaseUrls = true;
                options.LowercaseQueryStrings = true;
            });

            // Add API versioning
            builder.Services.AddApiVersioning(options =>
            {
                options.DefaultApiVersion = new ApiVersion(1, 0);
                options.AssumeDefaultVersionWhenUnspecified = true;
                options.ReportApiVersions = true;
                options.ApiVersionReader = ApiVersionReader.Combine(
                    new QueryStringApiVersionReader("api-version"),
                    new HeaderApiVersionReader("x-api-version"),
                    new UrlSegmentApiVersionReader()
                );
            });

            // Add versioned API explorer
            builder.Services.AddVersionedApiExplorer(options =>
            {
                options.GroupNameFormat = "'v'VVV";
                options.SubstituteApiVersionInUrl = true;
                options.AssumeDefaultVersionWhenUnspecified = true;
            });

            // Register application services
            builder.Services.AddScoped<IAuthService, AuthService>();
            builder.Services.AddScoped<IServerService, ServerService>();
            builder.Services.AddScoped<IRoleService, RoleService>();
            builder.Services.AddScoped<IVpnProtocolService, VpnProtocolService>();
            builder.Services.AddScoped<IMobileConnectionService, MobileConnectionService>();
            builder.Services.AddScoped<IConnectionService,ConnectionService>();

            // new

            // In Program.cs or Startup.cs
            builder.Services.AddScoped<IPackageService, PackageService>();
            //builder.Services.AddScoped<ISubscriptionService, SubscriptionService>();
            builder.Services.AddScoped<ICouponService, CouponService>();
            builder.Services.AddScoped<IDiscountService, DiscountService>();
            builder.Services.AddScoped<ISeasonalDealService, SeasonalDealService>();


            // Register validators
            builder.Services.AddValidatorsFromAssemblyContaining<Program>();

            // Add HTTP client for OAuth service
            builder.Services.AddHttpClient<IAuthService, AuthService>(client =>
            {
                client.Timeout = TimeSpan.FromSeconds(30);
                client.DefaultRequestHeaders.Add("User-Agent", "CasperVPN-API");
            });

            // Add general HTTP client
            builder.Services.AddHttpClient("Default", client =>
            {
                client.Timeout = TimeSpan.FromSeconds(30);
                client.DefaultRequestHeaders.Add("User-Agent", "CasperVPN-API");
            });

            // Add controllers with JSON options
            builder.Services.AddControllers()
                .AddJsonOptions(options =>
                {
                    options.JsonSerializerOptions.PropertyNamingPolicy = JsonNamingPolicy.CamelCase;
                    options.JsonSerializerOptions.DictionaryKeyPolicy = JsonNamingPolicy.CamelCase;
                    options.JsonSerializerOptions.WriteIndented = builder.Environment.IsDevelopment();
                    // Add this line to handle enum strings
                    options.JsonSerializerOptions.Converters.Add(new System.Text.Json.Serialization.JsonStringEnumConverter());
                });

            builder.Services.AddEndpointsApiExplorer();

            // Add HTTP context accessor
            builder.Services.AddHttpContextAccessor();

            // Add health checks
            builder.Services.AddHealthChecks();

            // Add response compression
            builder.Services.AddResponseCompression();

            // Add memory cache
            builder.Services.AddMemoryCache();

            // Add distributed cache
            builder.Services.AddDistributedMemoryCache();

            // Add session for OAuth state management (required for manual OAuth)
            builder.Services.AddSession(options =>
            {
                options.Cookie.Name = "CasperVpn.Session";
                options.Cookie.HttpOnly = true;
                options.Cookie.IsEssential = true;
                options.Cookie.SameSite = SameSiteMode.None;
                options.Cookie.SecurePolicy = CookieSecurePolicy.Always;
                options.IdleTimeout = TimeSpan.FromMinutes(30);
            });

            // Add logging
            builder.Services.AddLogging(logging =>
            {
                logging.AddConsole();
                logging.AddDebug();
                logging.AddConfiguration(builder.Configuration.GetSection("Logging"));
            });

            Console.WriteLine("Application services configured successfully.");
        }

        private static void ConfigureSwagger(WebApplicationBuilder builder)
        {
            builder.Services.AddSwaggerGen(c =>
            {
                // Single Swagger document for all APIs
                c.SwaggerDoc("v1", new OpenApiInfo
                {
                    Title = "Casper VPN API",
                    Version = "1.0",
                    Description = "Casper VPN Backend API with Admin and Public endpoints",
                    Contact = new OpenApiContact
                    {
                        Name = "Casper VPN Team",
                        Email = "support@caspervpn.com"
                    }
                    // Remove the License section temporarily to fix the URI error
                    // License = new OpenApiLicense
                    // {
                    //     Name = "MIT License",
                    //     Url = new Uri("https://opensource.org/licenses/MIT")
                    // }
                });

                // JWT Authentication in Swagger
                c.AddSecurityDefinition("Bearer", new OpenApiSecurityScheme
                {
                    Description = "JWT Authorization header using the Bearer scheme. Example: \"Authorization: Bearer {token}\"",
                    Name = "Authorization",
                    In = ParameterLocation.Header,
                    Type = SecuritySchemeType.Http,
                    Scheme = "bearer",
                    BearerFormat = "JWT"
                });

                c.AddSecurityRequirement(new OpenApiSecurityRequirement
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

                // Group all admin endpoints under single "Admin" tag
                c.TagActionsBy(api =>
                {
                    var relativePath = api.RelativePath ?? "";

                    // Check if it's an admin endpoint by route pattern
                    if (relativePath.Contains("/admin/", StringComparison.OrdinalIgnoreCase))
                    {
                        return new[] { "Admin" };
                    }

                    // Check if it's an admin endpoint by route pattern
                    else if (relativePath.Contains("/admin/", StringComparison.OrdinalIgnoreCase))
                    {
                        return new[] { "Mobile" };
                    }

                    else
                    {
                        // For non-admin endpoints, use controller name
                        var controllerName = api.ActionDescriptor.RouteValues["controller"];
                        return new[] { controllerName };
                    }

                    
                });

                // Order tags - Admin first, then others alphabetically
                c.OrderActionsBy(apiDesc =>
                {
                    var relativePath = apiDesc.RelativePath ?? "";

                    if (relativePath.Contains("/admin/", StringComparison.OrdinalIgnoreCase))
                    {
                        return $"1_Admin";
                    }

                    var controllerName = apiDesc.ActionDescriptor.RouteValues["controller"];
                    return $"2_{controllerName}";
                });

                // Include XML comments if available
                var xmlFile = $"{System.Reflection.Assembly.GetExecutingAssembly().GetName().Name}.xml";
                var xmlPath = Path.Combine(AppContext.BaseDirectory, xmlFile);
                if (File.Exists(xmlPath))
                {
                    c.IncludeXmlComments(xmlPath);
                }

                // Enable Swagger annotations
                c.EnableAnnotations();

                // Keep schema names unique
                c.CustomSchemaIds(type => type.FullName);
            });

            Console.WriteLine("Swagger configured successfully.");
        }

        private static void ConfigureCors(WebApplicationBuilder builder)
        {
            builder.Services.AddCors(options =>
            {
                options.AddPolicy("AllowAll", policy =>
                {
                    policy.AllowAnyOrigin()
                          .AllowAnyHeader()
                          .AllowAnyMethod();
                });

                options.AddPolicy("AllowFrontend", policy =>
                {
                    policy.WithOrigins(
                        "http://192.168.36.100:5173",
                            "http://localhost:3000",
                            "https://localhost:3000",
                            "http://localhost:5173",
                            "https://localhost:5173",
                            "http://localhost:5000",
                            "https://localhost:5000",
                            "http://localhost:5005",
                            "https://localhost:5005"
                        )
                        .AllowAnyHeader()
                        .AllowAnyMethod()
                        .AllowCredentials()
                        .WithExposedHeaders("WWW-Authenticate", "Pagination", "X-Total-Count")
                        .SetPreflightMaxAge(TimeSpan.FromMinutes(10));
                });

                options.AddPolicy("AllowSwagger", policy =>
                {
                    policy.WithOrigins(
                            "https://localhost:5005",
                            "http://localhost:5005"
                        )
                        .AllowAnyHeader()
                        .AllowAnyMethod()
                        .AllowCredentials();
                });
            });

            Console.WriteLine("CORS configured successfully.");
        }

        private static void ConfigureAuthorization(WebApplicationBuilder builder)
        {
            builder.Services.AddAuthorization(options =>
            {
                options.AddPolicy("Admin", policy => policy.RequireRole("Admin"));
                options.AddPolicy("User", policy => policy.RequireRole("User", "Admin"));
                options.AddPolicy("ExternalAuth", policy =>
                    policy.RequireAssertion(context =>
                        context.User.HasClaim(c => c.Type == ClaimTypes.AuthenticationMethod)));

                // Policy for OAuth authenticated users
                options.AddPolicy("OAuthUser", policy =>
                {
                    policy.RequireAuthenticatedUser();
                    policy.RequireClaim(ClaimTypes.Email);
                });
            });

            Console.WriteLine("Authorization configured successfully.");
        }

        private static async Task ConfigureMiddleware(WebApplication app)
        {
            // Apply database migrations and seed data
            using (var scope = app.Services.CreateScope())
            {
                try
                {
                    var dbContext = scope.ServiceProvider.GetRequiredService<CasperVpnDbContext>();
                    await dbContext.Database.MigrateAsync();
                    Console.WriteLine("Database migrations applied successfully.");

                    // Seed default data
                    await DataSeeder.SeedDefaultData(scope.ServiceProvider);
                    Console.WriteLine("Default data seeded successfully.");
                }
                catch (Exception ex)
                {
                    Console.WriteLine($"Error during database initialization: {ex}");
                    throw;
                }
            }

            // Configure the HTTP request pipeline

            // Forward proxy headers
            app.UseForwardedHeaders(new ForwardedHeadersOptions
            {
                ForwardedHeaders = ForwardedHeaders.XForwardedFor | ForwardedHeaders.XForwardedProto
            });

            // CORS must come before other middleware
            if (app.Environment.IsDevelopment())
            {
                app.UseCors("AllowAll");
                Console.WriteLine("CORS: AllowAll policy enabled for development");
            }
            else
            {
                app.UseCors("AllowFrontend");
                Console.WriteLine("CORS: AllowFrontend policy enabled");
            }

            // Session middleware (required for manual OAuth state management)
            app.UseSession();
            Console.WriteLine("Session middleware enabled");

            if (app.Environment.IsDevelopment())
            {
                app.UseDeveloperExceptionPage();

                // Swagger configuration - SINGLE EXPLORER
                app.UseSwagger();
                app.UseSwaggerUI(c =>
                {
                    // Single Swagger endpoint for all APIs
                    c.SwaggerEndpoint("/swagger/v1/swagger.json", "Casper VPN API v1");

                    c.RoutePrefix = "swagger";
                    c.DisplayRequestDuration();
                    c.EnableDeepLinking();
                    c.EnableFilter();
                    c.ShowExtensions();

                    // Optional: Set default expanded tags
                    c.DefaultModelExpandDepth(2);
                    c.DefaultModelRendering(Swashbuckle.AspNetCore.SwaggerUI.ModelRendering.Model);

                    // Optional: Expand all tags by default
                    c.DocExpansion(Swashbuckle.AspNetCore.SwaggerUI.DocExpansion.List);
                });

                // Detailed errors in development
                app.UseStatusCodePagesWithReExecute("/error-development/{0}");
                Console.WriteLine("Development mode enabled with detailed errors");
            }
            else
            {
                app.UseExceptionHandler("/error");
                app.UseHsts();
                app.UseStatusCodePagesWithReExecute("/error/{0}");
                Console.WriteLine("Production mode enabled with error handling");
            }

            // Response compression
            app.UseResponseCompression();

            // Routing
            app.UseRouting();

            // Authentication & Authorization
            app.UseAuthentication();
            app.UseAuthorization();

            // Health check endpoint
            app.MapHealthChecks("/health")
               .AllowAnonymous();

            // Endpoints
            app.MapControllers();

            // Fallback for SPA
            app.MapFallbackToFile("index.html");

            // Display startup information
            Console.WriteLine($"\n=== Casper VPN API Startup Information ===");
            Console.WriteLine($"Environment: {app.Environment.EnvironmentName}");
            Console.WriteLine($"URLs: {string.Join(", ", app.Urls)}");
            Console.WriteLine($"Swagger: {app.Urls.FirstOrDefault()}/swagger");
            Console.WriteLine($"Health Check: {app.Urls.FirstOrDefault()}/health");
            Console.WriteLine($"API Versioning: Enabled (v1.0)");
            Console.WriteLine($"Manual OAuth Endpoints:");
            Console.WriteLine($"  - Initiate: {app.Urls.FirstOrDefault()}/api/v1/Auth/oauth/google?returnUrl=%2Fsuccess");
            Console.WriteLine($"  - Callback: {app.Urls.FirstOrDefault()}/api/v1/Auth/oauth/google/callback");
            Console.WriteLine($"Regular Auth Endpoints:");
            Console.WriteLine($"  - Login: {app.Urls.FirstOrDefault()}/api/v1/Auth/login");
            Console.WriteLine($"  - Register: {app.Urls.FirstOrDefault()}/api/v1/Auth/register");
            Console.WriteLine($"Admin Endpoints:");
            Console.WriteLine($"  - Servers: {app.Urls.FirstOrDefault()}/api/v1/admin/server");
            Console.WriteLine($"  - VPN Protocols: {app.Urls.FirstOrDefault()}/api/v1/admin/vpnprotocols");
            Console.WriteLine($"  - Roles: {app.Urls.FirstOrDefault()}/api/v1/admin/roles");
            Console.WriteLine($"==========================================\n");
        }
    }
}