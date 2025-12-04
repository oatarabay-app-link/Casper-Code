using Casper.API.Models.Casper.API.Models;
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Identity.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore;

namespace Casper.API.Models.CasperVpnDbContext
{
    public class CasperVpnDbContext: IdentityDbContext<Users>
    {
        
            public CasperVpnDbContext(DbContextOptions<CasperVpnDbContext> options)
                : base(options)
            {
            }

            public DbSet<Server> Servers { get; set; }
            public DbSet<Connection> Connections { get; set; }
            public DbSet<VpnProtocol> VpnProtocols { get; set; }



        // New DbSets for Package and Subscription
        public DbSet<Package> Packages { get; set; }
        public DbSet<Subscription> Subscriptions { get; set; }
        public DbSet<Coupon> Coupons { get; set; }
        public DbSet<SeasonalDeal> SeasonalDeals { get; set; }
        public DbSet<PlanDiscount> PlanDiscounts { get; set; }






        // Permission tables
        public DbSet<Permission> Permissions { get; set; }
        public DbSet<RolePermission> RolePermissions { get; set; }


        public DbSet<RefreshTokens> RefreshTokens { get; set; }

        protected override void OnModelCreating(ModelBuilder builder)
        {
            base.OnModelCreating(builder);

            // Configure Identity tables for MySQL
            builder.Entity<IdentityRole>(entity =>
            {
                entity.Property(e => e.Id).HasMaxLength(255);
                entity.Property(e => e.Name).HasMaxLength(256);
                entity.Property(e => e.NormalizedName).HasMaxLength(256);
                entity.Property(e => e.ConcurrencyStamp).HasMaxLength(255);
            });

            builder.Entity<Users>(entity =>
            {
                entity.Property(e => e.Id).HasMaxLength(255);
                entity.Property(e => e.UserName).HasMaxLength(256);
                entity.Property(e => e.NormalizedUserName).HasMaxLength(256);
                entity.Property(e => e.Email).HasMaxLength(256);
                entity.Property(e => e.NormalizedEmail).HasMaxLength(256);
                entity.Property(e => e.PasswordHash).HasMaxLength(255);
                entity.Property(e => e.SecurityStamp).HasMaxLength(255);
                entity.Property(e => e.ConcurrencyStamp).HasMaxLength(255);
                entity.Property(e => e.PhoneNumber).HasMaxLength(50);
            });

            builder.Entity<IdentityUserClaim<string>>(entity =>
            {
                entity.Property(e => e.UserId).HasMaxLength(255);
                entity.Property(e => e.ClaimType).HasMaxLength(255);
                entity.Property(e => e.ClaimValue).HasMaxLength(255);
            });

            builder.Entity<IdentityUserLogin<string>>(entity =>
            {
                entity.Property(e => e.LoginProvider).HasMaxLength(255);
                entity.Property(e => e.ProviderKey).HasMaxLength(255);
                entity.Property(e => e.UserId).HasMaxLength(255);
                entity.Property(e => e.ProviderDisplayName).HasMaxLength(255);
            });

            builder.Entity<IdentityUserRole<string>>(entity =>
            {
                entity.Property(e => e.UserId).HasMaxLength(255);
                entity.Property(e => e.RoleId).HasMaxLength(255);
            });

            builder.Entity<IdentityUserToken<string>>(entity =>
            {
                entity.Property(e => e.UserId).HasMaxLength(255);
                entity.Property(e => e.LoginProvider).HasMaxLength(255);
                entity.Property(e => e.Name).HasMaxLength(255);
                entity.Property(e => e.Value).HasMaxLength(255);
            });

            builder.Entity<IdentityRoleClaim<string>>(entity =>
            {
                entity.Property(e => e.RoleId).HasMaxLength(255);
                entity.Property(e => e.ClaimType).HasMaxLength(255);
                entity.Property(e => e.ClaimValue).HasMaxLength(255);
            });

            builder.Entity<RefreshTokens>()
                .HasOne(rt => rt.User)
                .WithMany()
                .HasForeignKey(rt => rt.UserId)
                .OnDelete(DeleteBehavior.Cascade);






            // Configure RolePermission junction table
            builder.Entity<RolePermission>()
                .HasKey(rp => new { rp.RoleId, rp.PermissionId });

            builder.Entity<RolePermission>()
                .HasOne(rp => rp.Role)
                .WithMany()
                .HasForeignKey(rp => rp.RoleId);

            builder.Entity<RolePermission>()
                .HasOne(rp => rp.Permission)
                .WithMany(p => p.RolePermissions)
                .HasForeignKey(rp => rp.PermissionId);
        }
        }
    
}
