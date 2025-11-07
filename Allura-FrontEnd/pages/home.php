
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php require_once('../config.php');?>
</head>
<body>
    <div class="layout">
        <div id="sidebar-container">
            <?php include("../includes/sidebar.php"); ?>
        </div>
        <div class="home-container">
            <?php include("../includes/header.php"); ?>
            <main>
                <div class="simbolo-fondo">
                    <img src="../assets/images/simbolo-fondo.png">
                </div>
                <div class="main-contents">
                    <div class="content-1">
                        <div class="content-1-totales">
                            <div class="totales">
                                <p >Total de Ganancia Neta</p>
                                <div class="info-totales">
                                    <img src="../assets/images/simbolo.png" alt="icono">
                                    <p id="montoGananciaNeta">RD$ 70,903</p>
                                </div>
                            </div>
                            <div class="totales">
                                <p>Total de Venta Neta</p>
                                <div class="info-totales">
                                    <img src="../assets/images/simbolo.png" alt="icono">
                                    <p id="montoVentaNeta">RD$ 70,903</p>
                                </div>
                            </div>
                        </div>
                        <div class="content-1-graficas">
                            <p>Total de Ventas
                                <i class="bi bi-exclamation-circle"></i>
                            </p>
                            <div class="grafico-ventas">
                                <canvas id="chartVentas"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="content-2">
                        <div class="total-cobrar">
                            <p>Total por cobrar a clientes</p>
                            <div class="total-content">
                                <p id="totalCobrar">RD$ 70,903</p>
                            </div>
                        </div>
                        <div class="total-pagar">
                            <p>Total por Pagar al Suplidor</p>
                            <div class="total-content">
                                <p id="totalPagar">RD$ 70,903</p>
                            </div>
                        </div>
                        <div class="resumen">
                            <div class="acciones-resumen" style="display:flex">
                                <p>Resumen de ventas <i class="bi bi-exclamation-circle"></i></p>
                                <button id="btnAbrirModal" class="btn-filtro-icon" title="Filtrar Resumen">
                                    <i class="bi bi-sliders"></i>
                                </button>

                            </div>

                            <div class="content-resumen-unificado">
                                <div id="detalle-filtros" class="detalle-filtros">
                                <p><i class="bi bi-info-circle"></i> Sin filtros aplicados.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="copyrigth">
                    <p>Copyrigth © 2025 • Desarrollado por Sethor</p>
                </div>
            </main>
        </div>
        
    </div>

    <!-- Modal Filtro Resumen -->
    <div id="modal-resumen" class="modal-resumen-overlay">
        <div class="modal-resumen-content">
            <span id="modal-resumen-close" class="modal-resumen-close">&times;</span>
            <h2 class="modal-resumen-title">Filtrar Resumen</h2>

            <div class="modal-resumen-body">

            <div class="modal-resumen-section">
                <h3 class="modal-resumen-subtitle">Periodo Semanal</h3>

                <div class="modal-resumen-group">
                <label for="tipo-semanal">Filtrar por:</label>
                <select id="tipo-semanal">
                    <option value="">Seleccione...</option>
                    <option value="cliente">Cliente</option>
                    <option value="suplidor">Suplidor</option>
                </select>
                </div>

                <div class="modal-resumen-group">
                <label for="select-semanal">Seleccione:</label>
                <select id="select-semanal" disabled>
                    <option value="">Seleccione un tipo primero</option>
                </select>
                </div>
            </div>

            <hr />

            <div class="modal-resumen-section">
                <h3 class="modal-resumen-subtitle">Periodo Mensual</h3>

                <div class="modal-resumen-group">
                <label for="tipo-mensual">Filtrar por:</label>
                <select id="tipo-mensual">
                    <option value="">Seleccione...</option>
                    <option value="cliente">Cliente</option>
                    <option value="suplidor">Suplidor</option>
                </select>
                </div>

                <div class="modal-resumen-group">
                <label for="select-mensual">Seleccione:</label>
                <select id="select-mensual" disabled>
                    <option value="">Seleccione un tipo primero</option>
                </select>
                </div>
            </div>

            <hr />

            <div class="modal-resumen-section">
                <h3 class="modal-resumen-subtitle">Filtros Adicionales</h3>

                <div class="modal-resumen-group">
                <label for="resumen-select-producto">Producto:</label>
                <select id="resumen-select-producto">
                    <option value="">Seleccione...</option>
                </select>
                </div>

                <div class="modal-resumen-group">
                <label for="resumen-select-cliente-producto">Cliente (por producto):</label>
                <select id="resumen-select-cliente-producto">
                    <option value="">Seleccione...</option>
                </select>
                </div>
            </div>

            </div>

            <div class="modal-resumen-footer">
                <button id="modal-resumen-btn-aplicar" class="modal-resumen-btn-aplicar">
                    <i class="bi bi-filter-circle"></i> Aplicar
                </button>
                <button id="modal-resumen-btn-cerrar" class="modal-resumen-btn-cancelar">Cerrar</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const modal = document.getElementById("modal-resumen");
            const btnOpen = document.getElementById("btnAbrirModal");
            const btnClose = document.getElementById("modal-resumen-btn-cerrar");
            const btnApply = document.getElementById("modal-resumen-btn-aplicar");
            const closeX = document.getElementById("modal-resumen-close");

            let clientes = [];
            let suplidores = [];
            let productos = [];
            let clientesProd = [];

            // 🔹 Verificar si hay filtros guardados
            const savedFilters = JSON.parse(localStorage.getItem("dashboardFilters") || "null");
            if (savedFilters) {
                aplicarFiltrosGuardados(savedFilters);
            }

            // 🔹 Abrir / cerrar modal
            btnOpen.addEventListener("click", () => (modal.style.display = "flex"));
            btnClose.addEventListener("click", () => (modal.style.display = "none"));
            closeX.addEventListener("click", () => (modal.style.display = "none"));
            window.addEventListener("click", e => { if (e.target === modal) modal.style.display = "none"; });

            // 🔹 Cargar datos base
            fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/get_clientes_suplidores.php")
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        clientes = data.data.clientes || [];
                        suplidores = data.data.suplidores || [];
                    }
                });

            fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/get_items_clientes.php")
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        productos = data.data.items || [];
                        clientesProd = data.data.clientes || [];

                        document.getElementById("resumen-select-producto").innerHTML =
                            "<option value=''>Seleccione...</option>" +
                            productos.map(p => `<option value="${p.nombre}">${p.nombre}</option>`).join("");

                        document.getElementById("resumen-select-cliente-producto").innerHTML =
                            "<option value=''>Seleccione...</option>" +
                            clientesProd.map(c => `<option value='${c.ruc}'>${c.nombre}</option>`).join("");
                    }
                });

            // 🔹 Select dinámico semanal/mensual
            document.getElementById("tipo-semanal").addEventListener("change", e => {
                const select = document.getElementById("select-semanal");
                const tipo = e.target.value;
                select.disabled = !tipo;
                if (tipo === "cliente") {
                    select.innerHTML = "<option value=''>Seleccione cliente...</option>" +
                        clientes.map(c => `<option value='${c.ruc}'>${c.nombre}</option>`).join("");
                } else if (tipo === "suplidor") {
                    select.innerHTML = "<option value=''>Seleccione suplidor...</option>" +
                        suplidores.map(s => `<option value='${s.ruc}'>${s.nombre}</option>`).join("");
                } else {
                    select.innerHTML = "<option value=''>Seleccione un tipo primero</option>";
                }
            });

            document.getElementById("tipo-mensual").addEventListener("change", e => {
                const select = document.getElementById("select-mensual");
                const tipo = e.target.value;
                select.disabled = !tipo;
                if (tipo === "cliente") {
                    select.innerHTML = "<option value=''>Seleccione cliente...</option>" +
                        clientes.map(c => `<option value='${c.ruc}'>${c.nombre}</option>`).join("");
                } else if (tipo === "suplidor") {
                    select.innerHTML = "<option value=''>Seleccione suplidor...</option>" +
                        suplidores.map(s => `<option value='${s.ruc}'>${s.nombre}</option>`).join("");
                } else {
                    select.innerHTML = "<option value=''>Seleccione un tipo primero</option>";
                }
            });

            // 🔹 Aplicar filtros manualmente
            btnApply.addEventListener("click", () => {
                const tipoQ = document.getElementById("tipo-semanal").value;
                const valorQ = document.getElementById("select-semanal").value;
                const tipoM = document.getElementById("tipo-mensual").value;
                const valorM = document.getElementById("select-mensual").value;
                const producto = document.getElementById("resumen-select-producto").value;
                const clienteProd = document.getElementById("resumen-select-cliente-producto").value;

                if (!tipoQ || !valorQ || !tipoM || !valorM || !producto || !clienteProd) {
                    Swal.fire({
                        icon: "warning",
                        title: "Campos incompletos.",
                        text: "Rellene todos los campos para poder continuar.",
                    });
                    return;
                }

                const filtros = {
                    tipo_semanal: tipoQ,
                    valor_semanal: valorQ,
                    tipo_mensual: tipoM,
                    valor_mensual: valorM,
                    producto,
                    cliente_producto: clienteProd
                };

                localStorage.setItem("dashboardFilters", JSON.stringify(filtros));
                aplicarFiltrosGuardados(filtros,true);
                modal.style.display = "none";
            });

            function aplicarFiltrosGuardados(filtros, showAlert = false) {
                fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/dashboard_filtros.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(filtros)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        const detalle = `
                            <div class="detalle-filtros-grid">
                                <div class="filtro-item">
                                    <div class="filtro-item-label">Semanal - ${data.data.resumen_semanal.nombre}</div>
                                    <div class="filtro-item-valor">RD$${data.data.resumen_semanal.total}</div>
                                </div>
                                <div class="filtro-item">
                                    <div class="filtro-item-label">Mensual - ${data.data.resumen_mensual.nombre}</div>
                                    <div class="filtro-item-valor">RD$${data.data.resumen_mensual.total}</div>
                                </div>
                                <div class="filtro-item">
                                    <div class="filtro-item-label">${data.data.resumen_cattleya.producto}</div>
                                    <div class="filtro-item-valor">RD$${data.data.resumen_cattleya.total}</div>
                                </div>
                                <div class="filtro-item">
                                    <div class="filtro-item-label">${data.data.resumen_toyota.cliente}</div>
                                    <div class="filtro-item-valor">RD$${data.data.resumen_toyota.total}</div>
                                </div>
                            </div>
                        `;
                        document.getElementById("detalle-filtros").innerHTML = detalle;

                        // ✅ Mostrar alerta solo si se ejecutó manualmente
                        if (showAlert) {
                            Swal.fire({
                                icon: "success",
                                title: "Filtros aplicados correctamente",
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: "error",
                        title: "Error de conexión",
                        text: "No se pudo comunicar con el servidor."
                    });
                });
            }

        });
    </script>


    <script>
        
        document.addEventListener("DOMContentLoaded", () => {

            const token = localStorage.getItem("token");

            fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/dashboard.php", {
                headers: { "Authorization": token }
            })
            .then(res => res.json())
            .then(result => {
                if (result.status) {
                    document.getElementById("montoGananciaNeta").textContent = "RD$ " + parseFloat(result.data.ganancia_neta).toFixed(1);
                    document.getElementById("montoVentaNeta").textContent = "RD$ " + parseFloat(result.data.venta_neta).toFixed(1);
                    document.getElementById("totalCobrar").textContent = "RD$ " + parseFloat(result.data.total_cobrar).toFixed(1);
                    document.getElementById("totalPagar").textContent = "RD$ " + parseFloat(result.data.total_pagar).toFixed(1);

                    renderChart(result.data.grafico && result.data.grafico.length ? result.data.grafico : []);
                } else {
                    Swal.fire({
                    icon: "error",
                    title: "Error al cargar el Dashboard",
                    text: result.message || "No se pudieron cargar los datos.",
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: "error",
                    title: "Error de conexión",
                    text: "No se pudo conectar con el servidor.",
                });
            });
        })

    </script>

    <script>
        let chartVentas; // variable global del gráfico

        function renderChart(dataGrafico) {
            const ctx = document.getElementById("chartVentas").getContext("2d");

            // Gradiente azul con transparencia
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, "rgba(0, 123, 255, 0.4)");
            gradient.addColorStop(1, "rgba(0, 123, 255, 0)");

            const labels = dataGrafico.map(item => item.mes);
            const valores = dataGrafico.map(item => parseFloat(item.total));

            // ✅ Destruir si ya existe
            if (chartVentas) chartVentas.destroy();

            // ✅ Crear gráfico
            chartVentas = new Chart(ctx, {
                type: "line",
                data: {
                    labels,
                    datasets: [{
                        label: "Total de Ventas Netas (RD$)",
                        data: valores,
                        fill: true,
                        backgroundColor: gradient,
                        borderColor: "#007bff",
                        pointBackgroundColor: "#ffffff",
                        pointBorderColor: "#007bff",
                        pointHoverBackgroundColor: "#007bff",
                        pointHoverBorderColor: "#ffffff",
                        borderWidth: 2,
                        tension: 0.35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1000,
                        easing: "easeOutQuart"
                    },
                    interaction: {
                        mode: "index",
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: "#333",
                                font: { weight: "600" }
                            }
                        },
                        tooltip: {
                            backgroundColor: "#1f2937",
                            titleColor: "#fff",
                            bodyColor: "#e5e7eb",
                            cornerRadius: 8,
                            padding: 12,
                            callbacks: {
                                label: ctx => `RD$ ${ctx.parsed.y.toLocaleString("es-DO", { minimumFractionDigits: 1 })}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: "#555",
                                font: { size: 12, weight: "500" }
                            }
                        },
                        y: {
                            grid: { color: "rgba(0,0,0,0.05)" },
                            ticks: {
                                color: "#555",
                                callback: val => `RD$ ${val.toLocaleString("es-DO")}`
                            }
                        }
                    }
                }
            });
        }
    </script>

    
</body>
</html>