using FluentAssertions;
using Xunit;
using CasperVPN.DTOs;
using CasperVPN.Models;
using CasperVPN.Services;

namespace CasperVPN.Tests;

public class VpnServerServiceTests : TestBase
{
    private readonly VpnServerService _serverService;

    public VpnServerServiceTests()
    {
        _serverService = new VpnServerService(
            DbContext,
            ServerLoggerMock.Object,
            RadiusServiceMock.Object);

        // Seed test servers
        SeedTestServers();
    }

    private void SeedTestServers()
    {
        var servers = new List<VpnServer>
        {
            new VpnServer
            {
                Id = Guid.NewGuid(),
                Name = "US-East-1",
                Hostname = "us-east-1.vpn.com",
                IpAddress = "10.0.0.1",
                Country = "United States",
                City = "New York",
                CountryCode = "US",
                Latitude = 40.7128,
                Longitude = -74.0060,
                Port = 51820,
                Protocol = VpnProtocol.WireGuard,
                Status = ServerStatus.Online,
                Load = 30,
                MaxConnections = 1000,
                ServerAccessLevel = 1,
                IsActive = true,
                IsPremium = false,
                PublicKey = "test-public-key"
            },
            new VpnServer
            {
                Id = Guid.NewGuid(),
                Name = "EU-West-1",
                Hostname = "eu-west-1.vpn.com",
                IpAddress = "10.0.0.2",
                Country = "Germany",
                City = "Frankfurt",
                CountryCode = "DE",
                Latitude = 50.1109,
                Longitude = 8.6821,
                Port = 51820,
                Protocol = VpnProtocol.WireGuard,
                Status = ServerStatus.Online,
                Load = 50,
                MaxConnections = 1000,
                ServerAccessLevel = 2,
                IsActive = true,
                IsPremium = true,
                PublicKey = "test-public-key-2"
            },
            new VpnServer
            {
                Id = Guid.NewGuid(),
                Name = "Offline-Server",
                Hostname = "offline.vpn.com",
                IpAddress = "10.0.0.3",
                Country = "France",
                City = "Paris",
                CountryCode = "FR",
                Port = 51820,
                Protocol = VpnProtocol.WireGuard,
                Status = ServerStatus.Offline,
                IsActive = true,
                IsPremium = false
            }
        };

        DbContext.VpnServers.AddRange(servers);
        DbContext.SaveChanges();
    }

    [Fact]
    public async Task GetServers_WithoutUser_ShouldReturnBasicServers()
    {
        // Act
        var result = await _serverService.GetServersAsync(null);

        // Assert
        result.Should().NotBeNull();
        result.Should().NotBeEmpty();
        result.Should().OnlyContain(s => s.Status == ServerStatus.Online || s.Status == ServerStatus.Offline);
    }

    [Fact]
    public async Task GetServerById_WithExistingServer_ShouldReturnServer()
    {
        // Arrange
        var servers = await _serverService.GetAllServersAsync();
        var serverId = servers.First().Id;

        // Act
        var result = await _serverService.GetServerByIdAsync(serverId);

        // Assert
        result.Should().NotBeNull();
        result!.Id.Should().Be(serverId);
    }

    [Fact]
    public async Task GetRecommendedServer_ShouldReturnOnlineServer()
    {
        // Arrange
        var user = new User
        {
            Id = Guid.NewGuid(),
            Email = "recommend@example.com",
            PasswordHash = "hash"
        };
        DbContext.Users.Add(user);
        await DbContext.SaveChangesAsync();

        // Act
        var result = await _serverService.GetRecommendedServerAsync(user.Id);

        // Assert
        result.Should().NotBeNull();
        result!.Status.Should().Be(ServerStatus.Online);
    }

    [Fact]
    public async Task ConnectToServer_ShouldCreateConnectionLog()
    {
        // Arrange
        var user = new User
        {
            Id = Guid.NewGuid(),
            Email = "connect@example.com",
            PasswordHash = "hash",
            DataLimitBytes = 0 // Unlimited
        };
        DbContext.Users.Add(user);
        await DbContext.SaveChangesAsync();

        var servers = await _serverService.GetAllServersAsync();
        var server = servers.First(s => s.Status == ServerStatus.Online);

        var request = new ConnectRequest
        {
            DeviceType = "iOS",
            DeviceOs = "iOS 17"
        };

        // Act
        var result = await _serverService.ConnectToServerAsync(server.Id, user.Id, request, "192.168.1.1");

        // Assert
        result.Should().NotBeNull();
        result.ServerId.Should().Be(server.Id);
        result.Status.Should().Be(ConnectionStatus.Active);
        result.DeviceType.Should().Be("iOS");
    }

    [Fact]
    public async Task DisconnectFromServer_ShouldUpdateConnectionLog()
    {
        // Arrange
        var user = new User
        {
            Id = Guid.NewGuid(),
            Email = "disconnect@example.com",
            PasswordHash = "hash"
        };
        DbContext.Users.Add(user);
        await DbContext.SaveChangesAsync();

        var servers = await _serverService.GetAllServersAsync();
        var server = servers.First(s => s.Status == ServerStatus.Online);

        // First connect
        var connectRequest = new ConnectRequest { DeviceType = "iOS" };
        await _serverService.ConnectToServerAsync(server.Id, user.Id, connectRequest, "192.168.1.1");

        // Then disconnect
        var disconnectRequest = new DisconnectRequest
        {
            BytesUploaded = 1024 * 1024,
            BytesDownloaded = 10 * 1024 * 1024,
            DisconnectReason = "User initiated"
        };

        // Act
        var result = await _serverService.DisconnectFromServerAsync(server.Id, user.Id, disconnectRequest);

        // Assert
        result.Should().NotBeNull();
        result.Status.Should().Be(ConnectionStatus.Disconnected);
        result.BytesUploaded.Should().Be(1024 * 1024);
        result.BytesDownloaded.Should().Be(10 * 1024 * 1024);
    }

    [Fact]
    public async Task CreateServer_ShouldCreateNewServer()
    {
        // Arrange
        var request = new CreateServerRequest
        {
            Name = "New Server",
            Hostname = "new.vpn.com",
            IpAddress = "10.0.0.100",
            Country = "Japan",
            City = "Tokyo",
            CountryCode = "JP",
            Latitude = 35.6762,
            Longitude = 139.6503,
            Port = 51820,
            Protocol = VpnProtocol.WireGuard,
            MaxConnections = 500,
            ServerAccessLevel = 1
        };

        // Act
        var result = await _serverService.CreateServerAsync(request);

        // Assert
        result.Should().NotBeNull();
        result.Name.Should().Be("New Server");
        result.Country.Should().Be("Japan");
        result.Status.Should().Be(ServerStatus.Online);
    }

    [Fact]
    public async Task UpdateServer_ShouldUpdateExistingServer()
    {
        // Arrange
        var servers = await _serverService.GetAllServersAsync();
        var server = servers.First();

        var updateRequest = new UpdateServerRequest
        {
            Name = "Updated Server Name",
            Status = ServerStatus.Maintenance
        };

        // Act
        var result = await _serverService.UpdateServerAsync(server.Id, updateRequest);

        // Assert
        result.Should().NotBeNull();
        result.Name.Should().Be("Updated Server Name");
        result.Status.Should().Be(ServerStatus.Maintenance);
    }

    [Fact]
    public async Task DeleteServer_ShouldDeactivateServer()
    {
        // Arrange
        var servers = await _serverService.GetAllServersAsync();
        var server = servers.First();

        // Act
        var result = await _serverService.DeleteServerAsync(server.Id);

        // Assert
        result.Should().BeTrue();

        var deletedServer = await DbContext.VpnServers.FindAsync(server.Id);
        deletedServer!.IsActive.Should().BeFalse();
        deletedServer.Status.Should().Be(ServerStatus.Offline);
    }

    [Fact]
    public async Task GetServerConfig_ShouldReturnValidConfig()
    {
        // Arrange
        var user = new User
        {
            Id = Guid.NewGuid(),
            Email = "config@example.com",
            PasswordHash = "hash"
        };
        var plan = new Plan
        {
            Id = Guid.NewGuid(),
            Name = "Premium",
            ServerAccessLevel = 3 // All servers
        };
        var subscription = new Subscription
        {
            Id = Guid.NewGuid(),
            UserId = user.Id,
            PlanId = plan.Id,
            Status = SubscriptionStatus.Active
        };
        
        DbContext.Users.Add(user);
        DbContext.Plans.Add(plan);
        DbContext.Subscriptions.Add(subscription);
        await DbContext.SaveChangesAsync();

        var servers = await _serverService.GetAllServersAsync();
        var server = servers.First(s => s.IsActive);

        // Act
        var result = await _serverService.GetServerConfigAsync(server.Id, user.Id);

        // Assert
        result.Should().NotBeNull();
        result.Config.Should().NotBeNullOrEmpty();
        result.Config.Should().Contain("[Interface]");
        result.Config.Should().Contain("[Peer]");
        result.ServerHostname.Should().NotBeNullOrEmpty();
    }

    [Fact]
    public async Task UpdateServerHealth_ShouldUpdateServerMetrics()
    {
        // Arrange
        var servers = await _serverService.GetAllServersAsync();
        var server = servers.First();

        // Act
        await _serverService.UpdateServerHealthAsync(server.Id, 75, 500, true);

        // Assert
        var updatedServer = await DbContext.VpnServers.FindAsync(server.Id);
        updatedServer!.Load.Should().Be(75);
        updatedServer.CurrentConnections.Should().Be(500);
        updatedServer.Status.Should().Be(ServerStatus.Online);
        updatedServer.LastHealthCheck.Should().NotBeNull();
    }
}
