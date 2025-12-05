use actix_web::{get, web, App, HttpResponse, HttpServer, Responder};
use serde::{Deserialize, Serialize};
use sysinfo::{System, SystemExt, CpuExt, DiskExt};
use std::sync::Mutex;

#[derive(Serialize, Deserialize)]
struct ServerMetrics {
    cpu_usage: f32,
    memory_total: u64,
    memory_used: u64,
    disk_total: u64,
    disk_used: u64,
    uptime: u64,
}

struct AppState {
    sys: Mutex<System>,
}

#[get("/")]
async fn index() -> impl Responder {
    HttpResponse::Ok().json(serde_json::json!({
        "service": "CasperVPN Server Agent",
        "version": "1.0.0",
        "status": "running"
    }))
}

#[get("/health")]
async fn health() -> impl Responder {
    HttpResponse::Ok().json(serde_json::json!({
        "status": "healthy"
    }))
}

#[get("/metrics")]
async fn metrics(data: web::Data<AppState>) -> impl Responder {
    let mut sys = data.sys.lock().unwrap();
    sys.refresh_all();

    let cpu_usage = sys.global_cpu_info().cpu_usage();
    let memory_total = sys.total_memory();
    let memory_used = sys.used_memory();
    
    let disks = sys.disks();
    let (disk_total, disk_used) = if let Some(disk) = disks.first() {
        (disk.total_space(), disk.total_space() - disk.available_space())
    } else {
        (0, 0)
    };

    let metrics = ServerMetrics {
        cpu_usage,
        memory_total,
        memory_used,
        disk_total,
        disk_used,
        uptime: sys.uptime(),
    };

    HttpResponse::Ok().json(metrics)
}

#[actix_web::main]
async fn main() -> std::io::Result<()> {
    env_logger::init_from_env(env_logger::Env::new().default_filter_or("info"));

    let sys = web::Data::new(AppState {
        sys: Mutex::new(System::new_all()),
    });

    log::info!("Starting CasperVPN Server Agent on 0.0.0.0:8081");

    HttpServer::new(move || {
        App::new()
            .app_data(sys.clone())
            .service(index)
            .service(health)
            .service(metrics)
    })
    .bind(("0.0.0.0", 8081))?
    .run()
    .await
}
