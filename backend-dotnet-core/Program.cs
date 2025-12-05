using System.Text;
using System.Text.Json;
using System.Reflection;
using Microsoft.AspNetCore.Authentication.JwtBearer;
using Microsoft.AspNetCore.Diagnostics.HealthChecks;
using Microsoft.EntityFrameworkCore;
using Microsoft.IdentityModel.Tokens;
using Microsoft.OpenApi.Models;
using Serilog;
using CasperVPN.Data;
using CasperVPN.Helpers;
using CasperVPN.Services;
using CasperVPN.Middleware;

// Configure Serilog
Log.Logger = new LoggerConfiguration()
    .MinimumLevel.Information()
    .WriteTo.Console()
    .WriteTo.File("logs/caspervpn-.log", rollingInterval: RollingInterval.Day)
    .CreateLogger();

try
{
    Log.Information("Starting CasperVPN API");

    var builder = WebApplication.CreateBuilder(args);

    // Add Serilog
    builder.Host.UseSerilog();

    // Load configuration
    var configuration = builder.Configuration;

    // Configure settings
    builder.Services.Configure<JwtSettings>(configuration.GetSection("Jwt"));
    builder.Services.Configure<StripeSettings>(configuration.GetSection("Stripe"));
    builder.Services.Configure<RadiusSettings>(configuration.GetSection("Radius"));
    builder.Services.Configure<EmailSettings>(configuration.GetSection("Email"));
    builder.Services.Configure<AppSettings>(configuration.GetSection("App"));

    var jwtSettings = configuration.GetSection("Jwt").Get<JwtSettings>() ?? new JwtSettings();
    var appSettings = configuration.GetSection("App").Get<AppSettings>() ?? new AppSettings();

    // Add DbContext
    builder.Services.AddDbContext<ApplicationDbContext>(options =>
    {
        var connectionString = configuration.GetConnectionString("DefaultConnection");
        if (string.IsNullOrEmpty(connectionString))
        {
            // Use in-memory database for development
            options.UseInMemoryDatabase("CasperVPN");
        }
        else
        {
            options.UseNpgsql(connectionString);
        }
    });

    // Add Authentication
    builder.Services.AddAuthentication(options =>
    {
        options.DefaultAuthenticateScheme = JwtBearerDefaults.AuthenticationScheme;
        options.DefaultChallengeScheme = JwtBearerDefaults.AuthenticationScheme;
    })
    .AddJwtBearer(options =>
    {
        options.TokenValidationParameters = new TokenValidationParameters
        {
            ValidateIssuer = true,
            ValidateAudience = true,
            ValidateLifetime = true,
            ValidateIssuerSigningKey = true,
            ValidIssuer = jwtSettings.Issuer,
            ValidAudience = jwtSettings.Audience,
            IssuerSigningKey = new SymmetricSecurityKey(
                Encoding.UTF8.GetBytes(jwtSettings.SecretKey.PadRight(32, '0')))
        };
    });

    builder.Services.AddAuthorization();

    // Add Controllers
    builder.Services.AddControllers()
        .AddJsonOptions(options =>
        {
            options.JsonSerializerOptions.PropertyNamingPolicy = JsonNamingPolicy.CamelCase;
            options.JsonSerializerOptions.WriteIndented = true;
        });

    // Add API Explorer and Swagger
    builder.Services.AddEndpointsApiExplorer();
    builder.Services.AddSwaggerGen(options =>
    {
        options.SwaggerDoc("v1", new OpenApiInfo
        {
            Title = "CasperVPN API",
            Version = "v1.0",
            Description = "CasperVPN Backend API for VPN service management, user authentication, subscriptions, and server management.",
            Contact = new OpenApiContact
            {
                Name = "CasperVPN Support",
                Email = "support@caspervpn.com",
                Url = new Uri("https://caspervpn.com")
            },
            License = new OpenApiLicense
            {
                Name = "Proprietary",
                Url = new Uri("https://caspervpn.com/terms")
            }
        });

        // Add JWT Authentication to Swagger
        options.AddSecurityDefinition("Bearer", new OpenApiSecurityScheme
        {
            Name = "Authorization",
            Type = SecuritySchemeType.ApiKey,
            Scheme = "Bearer",
            BearerFormat = "JWT",
            In = ParameterLocation.Header,
            Description = "Enter 'Bearer' followed by your JWT token.\n\nExample: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
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

        // Include XML comments
        var xmlFile = $"{Assembly.GetExecutingAssembly().GetName().Name}.xml";
        var xmlPath = Path.Combine(AppContext.BaseDirectory, xmlFile);
        if (File.Exists(xmlPath))
        {
            options.IncludeXmlComments(xmlPath);
        }

        // Enable annotations
        options.EnableAnnotations();
    });

    // Add Health Checks
    builder.Services.AddHealthChecks();

    // Add CORS
    builder.Services.AddCors(options =>
    {
        options.AddPolicy("AllowAll", builder =>
        {
            builder.AllowAnyOrigin()
                   .AllowAnyMethod()
                   .AllowAnyHeader();
        });

        options.AddPolicy("Production", builder =>
        {
            builder.WithOrigins("https://caspervpn.com", "https://api.caspervpn.com", "https://admin.caspervpn.com")
                   .AllowAnyMethod()
                   .AllowAnyHeader()
                   .AllowCredentials();
        });
    });

    // Register Services
    // Use mock services for development, real services for production
    var useMockServices = appSettings.UseMockServices;

    builder.Services.AddScoped<IAuthService, AuthService>();
    builder.Services.AddScoped<IUserService, UserService>();
    builder.Services.AddScoped<ISubscriptionService, SubscriptionService>();
    builder.Services.AddScoped<IVpnServerService, VpnServerService>();
    builder.Services.AddScoped<IStripeService, StripeService>();
    builder.Services.AddScoped<IAdminService, AdminService>();

    if (useMockServices)
    {
        builder.Services.AddScoped<IEmailService, MockEmailService>();
        builder.Services.AddScoped<IFreeRadiusService, MockFreeRadiusService>();
    }
    else
    {
        builder.Services.AddScoped<IEmailService, EmailService>();
        builder.Services.AddHttpClient<IFreeRadiusService, FreeRadiusService>();
    }

    // Add HttpClient for external services
    builder.Services.AddHttpClient();

    // Add Rate Limiting (optional)
    // builder.Services.AddRateLimiter(options => { ... });

    var app = builder.Build();

    // Apply migrations and seed data in development
    using (var scope = app.Services.CreateScope())
    {
        var dbContext = scope.ServiceProvider.GetRequiredService<ApplicationDbContext>();
        try
        {
            // For InMemory database, just ensure created
            // For real database, run migrations
            if (dbContext.Database.IsInMemory())
            {
                dbContext.Database.EnsureCreated();
            }
            else
            {
                dbContext.Database.Migrate();
            }
        }
        catch (Exception ex)
        {
            Log.Error(ex, "Error applying database migrations");
        }
    }

    // Configure the HTTP request pipeline
    app.UseMiddleware<ExceptionMiddleware>();
    app.UseMiddleware<RequestLoggingMiddleware>();

    // Swagger (available in all environments for now)
    app.UseSwagger();
    app.UseSwaggerUI(options =>
    {
        options.SwaggerEndpoint("/swagger/v1/swagger.json", "CasperVPN API v1");
        options.RoutePrefix = "swagger";
        options.DocumentTitle = "CasperVPN API Documentation";
        options.DefaultModelsExpandDepth(-1); // Hide schemas section by default
    });

    app.UseHttpsRedirection();
    app.UseCors("AllowAll"); // Use "Production" in production

    app.UseAuthentication();
    app.UseAuthorization();

    app.MapControllers();

    // Health check endpoint
    app.MapHealthChecks("/health", new HealthCheckOptions
    {
        ResponseWriter = async (context, report) =>
        {
            context.Response.ContentType = "application/json";
            var response = new
            {
                status = report.Status.ToString(),
                checks = report.Entries.Select(x => new
                {
                    name = x.Key,
                    status = x.Value.Status.ToString(),
                    description = x.Value.Description
                }),
                duration = report.TotalDuration
            };
            await context.Response.WriteAsync(JsonSerializer.Serialize(response));
        }
    });

    // Root endpoint
    app.MapGet("/", () => new
    {
        service = "CasperVPN API",
        version = appSettings.ApiVersion,
        status = "running",
        environment = app.Environment.EnvironmentName,
        documentation = "/swagger"
    });

    app.Run();
}
catch (Exception ex)
{
    Log.Fatal(ex, "Application terminated unexpectedly");
}
finally
{
    Log.CloseAndFlush();
}
