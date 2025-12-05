using Microsoft.EntityFrameworkCore;
using CasperVPN.Models;

namespace CasperVPN.Data;

/// <summary>
/// Application database context for Entity Framework Core
/// </summary>
public class ApplicationDbContext : DbContext
{
    public ApplicationDbContext(DbContextOptions<ApplicationDbContext> options)
        : base(options)
    {
    }

    public DbSet<User> Users { get; set; }
    public DbSet<Plan> Plans { get; set; }
    public DbSet<Subscription> Subscriptions { get; set; }
    public DbSet<VpnServer> VpnServers { get; set; }
    public DbSet<ConnectionLog> ConnectionLogs { get; set; }
    public DbSet<Payment> Payments { get; set; }
    public DbSet<Invoice> Invoices { get; set; }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        base.OnModelCreating(modelBuilder);

        // User configuration
        modelBuilder.Entity<User>(entity =>
        {
            entity.HasIndex(e => e.Email).IsUnique();
            entity.HasIndex(e => e.StripeCustomerId);
            entity.HasIndex(e => e.RadiusUsername);
            entity.Property(e => e.Email).IsRequired().HasMaxLength(100);
            entity.Property(e => e.PasswordHash).IsRequired().HasMaxLength(256);
        });

        // Plan configuration
        modelBuilder.Entity<Plan>(entity =>
        {
            entity.HasIndex(e => e.Name);
            entity.HasIndex(e => e.StripeProductId);
            entity.Property(e => e.PriceMonthly).HasPrecision(18, 2);
            entity.Property(e => e.PriceYearly).HasPrecision(18, 2);
        });

        // Subscription configuration
        modelBuilder.Entity<Subscription>(entity =>
        {
            entity.HasIndex(e => e.UserId);
            entity.HasIndex(e => e.StripeSubscriptionId);
            entity.HasOne(e => e.User)
                  .WithOne(u => u.Subscription)
                  .HasForeignKey<Subscription>(e => e.UserId)
                  .OnDelete(DeleteBehavior.Cascade);
            entity.HasOne(e => e.Plan)
                  .WithMany(p => p.Subscriptions)
                  .HasForeignKey(e => e.PlanId)
                  .OnDelete(DeleteBehavior.Restrict);
        });

        // VpnServer configuration
        modelBuilder.Entity<VpnServer>(entity =>
        {
            entity.HasIndex(e => e.Country);
            entity.HasIndex(e => e.CountryCode);
            entity.HasIndex(e => e.Status);
            entity.HasIndex(e => e.IsActive);
            entity.Property(e => e.IpAddress).IsRequired().HasMaxLength(45);
            entity.Property(e => e.Hostname).IsRequired().HasMaxLength(100);
        });

        // ConnectionLog configuration
        modelBuilder.Entity<ConnectionLog>(entity =>
        {
            entity.HasIndex(e => e.UserId);
            entity.HasIndex(e => e.ServerId);
            entity.HasIndex(e => e.ConnectedAt);
            entity.HasOne(e => e.User)
                  .WithMany(u => u.ConnectionLogs)
                  .HasForeignKey(e => e.UserId)
                  .OnDelete(DeleteBehavior.Cascade);
            entity.HasOne(e => e.Server)
                  .WithMany(s => s.ConnectionLogs)
                  .HasForeignKey(e => e.ServerId)
                  .OnDelete(DeleteBehavior.Restrict);
        });

        // Payment configuration
        modelBuilder.Entity<Payment>(entity =>
        {
            entity.HasIndex(e => e.UserId);
            entity.HasIndex(e => e.StripePaymentIntentId);
            entity.HasIndex(e => e.StripeInvoiceId);
            entity.Property(e => e.Amount).HasPrecision(18, 2);
            entity.Property(e => e.RefundedAmount).HasPrecision(18, 2);
            entity.HasOne(e => e.User)
                  .WithMany(u => u.Payments)
                  .HasForeignKey(e => e.UserId)
                  .OnDelete(DeleteBehavior.Cascade);
        });

        // Invoice configuration
        modelBuilder.Entity<Invoice>(entity =>
        {
            entity.HasIndex(e => e.UserId);
            entity.HasIndex(e => e.InvoiceNumber).IsUnique();
            entity.HasIndex(e => e.StripeInvoiceId);
            entity.Property(e => e.Amount).HasPrecision(18, 2);
            entity.Property(e => e.Tax).HasPrecision(18, 2);
            entity.Property(e => e.Total).HasPrecision(18, 2);
        });

        // Seed default plans
        SeedPlans(modelBuilder);
    }

    private void SeedPlans(ModelBuilder modelBuilder)
    {
        var freePlanId = Guid.Parse("00000000-0000-0000-0000-000000000001");
        var monthlyPlanId = Guid.Parse("00000000-0000-0000-0000-000000000002");
        var yearlyPlanId = Guid.Parse("00000000-0000-0000-0000-000000000003");

        modelBuilder.Entity<Plan>().HasData(
            new Plan
            {
                Id = freePlanId,
                Name = "Free",
                Description = "Basic VPN access with limited features",
                PriceMonthly = 0,
                PriceYearly = 0,
                MaxDevices = 1,
                DataLimitBytes = 500 * 1024 * 1024, // 500 MB
                ServerAccessLevel = 1,
                Type = PlanType.Free,
                Features = "Basic servers,500MB data limit,1 device,Email support",
                IsActive = true,
                SortOrder = 0
            },
            new Plan
            {
                Id = monthlyPlanId,
                Name = "Premium Monthly",
                Description = "Full VPN access with all premium features",
                PriceMonthly = 9.99m,
                PriceYearly = 119.88m,
                StripePriceIdMonthly = "price_monthly_placeholder",
                MaxDevices = 5,
                DataLimitBytes = 0, // Unlimited
                ServerAccessLevel = 3,
                Type = PlanType.Premium,
                DefaultBillingInterval = BillingInterval.Monthly,
                Features = "All servers,Unlimited data,5 devices,Priority support,Kill switch,Split tunneling",
                IsActive = true,
                SortOrder = 1
            },
            new Plan
            {
                Id = yearlyPlanId,
                Name = "Premium Yearly",
                Description = "Full VPN access with all premium features - Best value!",
                PriceMonthly = 6.67m,
                PriceYearly = 79.99m,
                StripePriceIdYearly = "price_yearly_placeholder",
                MaxDevices = 5,
                DataLimitBytes = 0, // Unlimited
                ServerAccessLevel = 3,
                Type = PlanType.Premium,
                DefaultBillingInterval = BillingInterval.Yearly,
                Features = "All servers,Unlimited data,5 devices,Priority support,Kill switch,Split tunneling,33% savings",
                IsActive = true,
                SortOrder = 2
            }
        );
    }
}
