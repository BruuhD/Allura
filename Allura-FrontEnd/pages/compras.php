<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Historial de Compras</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css/compras.css">
    <?php require_once('../config.php');?>
  </head>
  <body>
    <div class="layout">
      <div id="sidebar-container">
        <?php include("../includes/sidebar.php"); ?>
      </div>
      <div class="home-container">
        <?php include("../includes/header.php")?>
        <main>
          <div class="simbolo-fondo">
            <img src="../assets/images/simbolo-fondo.png">
          </div>
          <div class="container-custom">
            <div class="content-buttons">
              <div class="buttons-compras">
                <button class="btn-create">
                  <i class="bi bi-plus-circle-fill"></i> Crear Orden
                </button>
                <button class="btn-history" data-bs-toggle="modal" data-bs-target="#historialModal">
                  <i class="bi bi-clock-history"></i> Historial de Compras
                </button>
              </div>
              <div class="total-box">
                <h6>Total de Compra</h6>
                <h4 id="totalCompraGeneral">RD$ 0.00</h4>
              </div>
            </div>

            <!-- TABLA -->
            <div class="table-responsive">
              <table>
                <thead>
                  <tr>
                    <th>Fecha</th>
                    <th>Factura</th>
                    <th>Producto</th>
                    <th>Proveedor</th>
                    <th>Cantidad</th>
                    <th>Abono</th>
                    <th>Total</th>
                    <th>Comprobante</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                  </tr>
                </thead>

                <tbody id="tablaCompras">
                  <!-- Filas se insertan aquí -->
                </tbody>
              </table>
            </div>
          </div>
          <div class="copyrigth">
            <p>Copyrigth © 2025 • Desarrollado por Sethor</p>
          </div>
        </main>
      </div>
    </div>

    <!-- Modal Crear Orden -->
    <div class="modal fade" id="crearOrdenModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog" >
        <div class="modal-content modal-content-orden">
          <div class="simbolo-fondo-modal">
            <img src="../assets/images/simbolo-fondo.png">
          </div>
          
          <div class="modal-header-orden">
            <div class="modal-header-grid-orden">
              <!-- Izquierda -->
              <div class="header-logo">
                <img src="../assets/images/Logo.png" alt="Allura Logo" class="modal-logo" />
              </div>

              <!-- Centro -->
              <div class="header-center">
                <h4 class="modal-title">Nueva Orden de Compra</h4>
                <p class="empresa-nombre"><strong>Allura Soluciones Integrales</strong></p>
                <p><span class="label">RNC o Cédula:</span> 809-780-4621</p>
                <p><span class="label">Correo:</span> allurard@gmail.com</p>
              </div>

              <!-- Derecha -->
              <div class="header-right">
                <p><span class="label">No. Orden:</span> <strong></strong></p>
                <p><span class="label">NFC:</span> <strong></strong></p>
              </div>
            </div>

            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
          </div>


          <!-- Body -->
          <div class="modal-body px-5">
            <form id="formOrden">

              <!-- Datos del proveedor -->
              <div class="section mb-4">
                <h6 class="section-title mb-3">Datos del Suplidor</h6>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="proveedor" placeholder="Nombre del proveedor" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Suplidor Registrado</label>
                    <select class="form-select" id="tipoProveedor">
                      <option value="nuevo">Nuevo</option>
                      <option value="registrado">Registrado</option>
                    </select>
                  </div>

                  <!-- 🔹 Nuevo campo: lista de suplidores (se muestra solo si es "registrado") -->
                  <div class="col-md-12" id="selectSuplidorContainer" style="display:none;">
                    <label class="form-label">Seleccionar Suplidor</label>
                    <select class="form-select" id="selectSuplidor">
                      <option value="">-- Selecciona un Suplidor --</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">RNC o Cédula</label>
                    <input type="text" class="form-control" id="rnc" placeholder="123456789" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="number" class="form-control" id="telefono" placeholder="+1 809-000-0000">
                  </div>
                </div>
              </div>

              <hr>

              <div class="section mb-4">
                <h6 class="section-title mb-3">Item</h6>
                <div class="row g-3 align-items-end">
                  <div class="col-md-4">
                    <label class="form-label">Producto</label>
                    <input type="text" class="form-control" id="producto" placeholder="Ej. Papa Natural">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Unidad</label>
                    <select class="form-select" id="unidad">
                      <option value="LB">LB</option>
                      <option value="KG">KG</option>
                      <option value="UNID">UNID</option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Costo</label>
                    <input type="number" class="form-control" id="precio" step="0.01" placeholder="0.00">

                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" class="form-control" id="cantidad" step="0.01" placeholder="0">
                  </div>
                </div>

                <div class="text-end mt-3">
                  <button type="button" class="btn btn-success" id="btnAgregarItem">
                    Agregar Item
                  </button>
                </div>
                <div class="table-responsive mt-4">
                  <table class="table table-bordered align-middle" id="tablaItems">
                    <thead class="table-light">
                      <tr>
                        <th>Producto</th>
                        <th>Unidad</th>
                        <th>Costo</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Items dinámicos aquí -->
                    </tbody>
                  </table>
                </div>

                <div class="text-end mt-3">
                  <h5 class="fw-bold">Total: <span id="totalGeneral" class="text-success">RDS 0.00</span></h5>
                </div>
              </div>
              <hr>
              <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <div class="d-flex justify-content-center gap-3">
                  <button type="submit" class="btn btn-success">Guardar Orden</button>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Historial -->
    <div class="modal fade" id="historialModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-historial">
        <div class="modal-content modal-content-historial">
          <div class="modal-header">
            <h5 class="modal-title">Historial de Compras</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p>Historial general de órdenes registradas.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Subir Comprobante -->
    <div class="modal fade" id="modalComprobante" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="bi bi-file-earmark-arrow-up me-2"></i> Subir Comprobante</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <form id="formComprobante" enctype="multipart/form-data">
              <input type="hidden" id="orderNumberComprobante">

              <div class="mb-3">
                <label class="form-label">Monto a pagar</label>
                <input type="number" class="form-control" id="montoPagar" step="0.01" min="0" required>
                <small id="montoHelp" class="text-muted"></small>
              </div>

              <div class="mb-3">
                <label class="form-label">Archivo Comprobante (PDF o Imagen)</label>
                <input 
                  type="file" 
                  class="form-control" 
                  id="archivoComprobante" 
                  accept="application/pdf,image/png,image/jpeg,image/jpg"
                  required
                >
              </div>

            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" form="formComprobante" class="btn btn-success">
              <i class="bi bi-upload me-2"></i> Subir
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const tablaCompras = document.getElementById("tablaCompras");
        const totalCompraGeneral = document.getElementById("totalCompraGeneral");

        const modalComprobante = new bootstrap.Modal(document.getElementById("modalComprobante"));
        const formComprobante = document.getElementById("formComprobante");
        const montoPagarInput = document.getElementById("montoPagar");
        const montoHelp = document.getElementById("montoHelp");
        const archivoComprobante = document.getElementById("archivoComprobante");
        const orderNumberInput = document.getElementById("orderNumberComprobante");

        function formatoRD(valor) {
          return `RD$ ${parseFloat(valor || 0).toFixed(1)}`;
        }

        async function listarPendientes() {
          try {
            const res = await fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/orders/listar_ordenes.php");
            const data = await res.json();
            if (!data.status) throw new Error(data.msg);

            const pendientes = data.data.filter(o => o.estado.toLowerCase() !== "pagado");
            tablaCompras.innerHTML = "";
            let totalGeneral = 0;

            if (!pendientes.length) {
              tablaCompras.innerHTML = `
                <tr><td colspan="10" class="text-center text-muted py-4">No hay órdenes pendientes registradas.</td></tr>
              `;
              totalCompraGeneral.textContent = formatoRD(0);
              return;
            }

            pendientes.forEach(ord => {
              const fila = document.createElement("tr");
              totalGeneral += parseFloat(ord.total || 0);

              fila.dataset.orderId = ord.id;
              fila.dataset.rnc = ord.rnc || "";
              fila.dataset.telefono = ord.telefono || "";

              fila.innerHTML = `
                <td>${ord.fecha}</td>
                <td>${ord.order_number}</td>
                <td>${ord.productos || '-'}</td>
                <td>${ord.proveedor || '-'}</td>
                <td>${ord.cantidad_total || 0}</td>
                <td>${formatoRD(ord.abono || 0)}</td>
                <td>${formatoRD(ord.total)}</td>
                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2">
                    ${
                      ord.orden_pdf_url
                        ? `<button class="btn btn-sm btn-outline-secondary btnVerOrden" data-url="${ord.orden_pdf_url}">
                            <i class="bi bi-file-earmark-pdf"></i>
                          </button>`
                        : `<button class="btn btn-sm btn-outline-secondary disabled"><i class="bi bi-file-earmark-pdf"></i></button>`
                    }
                    <button class="btn btn-sm btn-outline-success btnSubirComprobante" 
                            data-order="${ord.order_number}"
                            data-total="${ord.total}" 
                            data-abono="${ord.abono || 0}">
                      <i class="bi bi-upload"></i>
                    </button>
                  </div>
                </td>
                <td><span class="badge bg-warning text-dark">${ord.estado}</span></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-outline-primary btnEditar"><i class="bi bi-pencil-square"></i></button>
                  <button class="btn btn-sm btn-outline-danger btnEliminar"><i class="bi bi-trash"></i></button>
                </td>
              `;
              tablaCompras.appendChild(fila);
            });


            totalCompraGeneral.textContent = formatoRD(totalGeneral);

          } catch (err) {
            console.error("Error al listar pendientes:", err);
            tablaCompras.innerHTML = `<tr><td colspan="10" class="text-danger text-center">Error al cargar órdenes.</td></tr>`;
          }
        }

        listarPendientes();
        // 🔹 Hacer que listarPendientes sea accesible globalmente
        window.listarPendientes = listarPendientes;

        // 🔹 También refrescar automáticamente al crear una nueva orden
        document.addEventListener("ordenCreada", listarPendientes);


        // 📤 Abrir modal para subir comprobante
        document.body.addEventListener("click", (e) => {
          const btn = e.target.closest(".btnSubirComprobante");
          if (btn) {
            const order = btn.dataset.order;
            const total = parseFloat(btn.dataset.total);
            const abono = parseFloat(btn.dataset.abono);

            orderNumberInput.value = order;
            montoPagarInput.value = "";
            const restante = total - abono;
            montoPagarInput.min = 0.01;
            montoPagarInput.max = restante;
            montoHelp.textContent = `Monto máximo permitido: RD$ ${restante.toFixed(1)} (pendiente por pagar).`;

            archivoComprobante.value = "";
            modalComprobante.show();
          }

          // 📄 Ver orden generada
          if (e.target.closest(".btnVerOrden")) {
            const url = e.target.closest(".btnVerOrden").dataset.url;
            if (url) window.open(url, "_blank");
          }

        });

        // 📥 Enviar comprobante y monto
        formComprobante.addEventListener("submit", async (e) => {
          e.preventDefault();
          const order = orderNumberInput.value;
          const monto = parseFloat(montoPagarInput.value);
          const file = archivoComprobante.files[0];
          const min = parseFloat(montoPagarInput.min);
          const max = parseFloat(montoPagarInput.max);

          if (!file || isNaN(monto)) {
            Swal.fire("Campos incompletos", "Debe seleccionar un archivo y monto válido.", "warning");
            return;
          }

          if (monto < min || monto > max) {
            Swal.fire("Monto inválido", `El monto debe estar entre RD$ ${min.toFixed(1)} y RD$ ${max.toFixed(1)}.`, "error");
            return;
          }

          const formData = new FormData();
          formData.append("order_number", order);
          formData.append("monto", monto);
          formData.append("file", file);

          console.log('file',formData)
          try {
            Swal.fire({ title: "Subiendo comprobante...", didOpen: () => Swal.showLoading() });
            const res = await fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/orders/upload_comprobante.php", {
              method: "POST",
              body: formData
            });
            const result = await res.json();
            Swal.close();

            if (!result.status) throw new Error(result.msg || "Error al subir comprobante");

            Swal.fire({ icon: "success", title: "Comprobante registrado correctamente", timer: 1800, showConfirmButton: false });
            modalComprobante.hide();
            
            listarPendientes();

          } catch (err) {
            Swal.fire("Error", err.message || "No se pudo completar el proceso.", "error");
          }
        });

        // 📄 Mostrar historial (solo pagadas)
        const historialModal = document.getElementById("historialModal");
        historialModal.addEventListener("show.bs.modal", async () => {
          try {
            const res = await fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/orders/listar_ordenes.php");
            const data = await res.json();
            if (!data.status) throw new Error(data.msg);
            const pagadas = data.data.filter(o => o.estado.toLowerCase() === "pagado");

            historialModal.querySelector(".modal-body").innerHTML = pagadas.length
              ? `
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead class="table-light">
                    <tr>
                      <th>Fecha</th>
                      <th>Factura</th>
                      <th>Proveedor</th>
                      <th>Productos</th>
                      <th>Total</th>
                      <th>Comprobante</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${pagadas.map(p => `
                      <tr>
                        <td>${p.fecha}</td>
                        <td>${p.order_number}</td>
                        <td>${p.proveedor || "-"}</td>
                        <td>${p.productos || "-"}</td>
                        <td>${formatoRD(p.total)}</td>
                        <td class="text-center">
                          ${
                            p.comprobantes
                              ? JSON.parse(p.comprobantes).map((c, i) => `
                                  <div class="mb-1">
                                    <button class="btn btn-sm btn-outline-success btnVerComprobante" data-url="${c.url}">
                                      <i class="bi bi-eye"></i> #${i + 1} (${formatoRD(c.monto)})
                                    </button>
                                    <small class="text-muted d-block">${c.fecha}</small>
                                  </div>
                                `).join("")
                              : `<span class="text-muted small">No disponible</span>`
                          }
                        </td>
                      </tr>
                    `).join("")}
                  </tbody>
                </table>
              </div>`
              : `<p class="text-center text-muted my-3">No hay órdenes pagadas registradas.</p>`;
          } catch (err) {
            historialModal.querySelector(".modal-body").innerHTML = `<p class="text-danger text-center">Error al cargar el historial.</p>`;
          }
        });

        // 👁️ Ver comprobante
        document.body.addEventListener("click", (e) => {
          const btn = e.target.closest(".btnVerComprobante");
          if (btn) window.open(btn.dataset.url, "_blank");
        });
      });
    </script>

    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const btnAgregar = document.getElementById("btnAgregarItem");
        const tablaItems = document.querySelector("#tablaItems tbody");
        const totalGeneral = document.getElementById("totalGeneral");

        // ✅ Función para formatear moneda
        function formatoRD(valor) {
          return `RD$ ${valor.toFixed(1)}`;
        }

        // ✅ Recalcular total general
        function recalcularTotal() {
          let total = 0;
          tablaItems.querySelectorAll("tr").forEach(fila => {
            const subtotal = parseFloat(fila.dataset.total || 0);
            total += subtotal;
          });
          totalGeneral.textContent = formatoRD(total);
        }

        // ✅ Evento: agregar item
        btnAgregar.addEventListener("click", () => {
          const producto = document.getElementById("producto").value.trim();
          const unidad = document.getElementById("unidad").value;
          const precio = parseFloat(document.getElementById("precio").value);
          const cantidad = parseFloat(document.getElementById("cantidad").value);

          if (!producto || isNaN(precio) || isNaN(cantidad)) {
            Swal.fire({
              icon: "warning",
              title: "Campos incompletos",
              text: "Debe ingresar producto, precio y cantidad válidos."
            });
            return;
          }

          const total = precio * cantidad;

          // Crear fila
          const fila = document.createElement("tr");
          fila.dataset.total = total;

          fila.innerHTML = `
            <td>${producto}</td>
            <td>${unidad}</td>
            <td>${formatoRD(precio)}</td>
            <td>${cantidad}</td>
            <td>${formatoRD(total)}</td>
            <td class="text-center">
              <button type="button" class="btn btn-sm btn-danger btnEliminarItem">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          `;

          tablaItems.appendChild(fila);
          recalcularTotal();

          // Limpiar inputs
          document.getElementById("producto").value = "";
          document.getElementById("precio").value = "";
          document.getElementById("cantidad").value = "";

          document.getElementById("producto").focus();
        });
        tablaItems.addEventListener("click", async (e) => {
          const btn = e.target.closest(".btnEliminarItem");
          if (!btn) return;

          const filasActuales = tablaItems.querySelectorAll("tr");
          if (filasActuales.length <= 1) {
            Swal.fire({
              icon: "warning",
              title: "No permitido",
              text: "La orden debe tener al menos un producto.",
            });
            return;
          }

          const fila = btn.closest("tr");
          const producto = fila.children[0]?.textContent?.trim() || "este producto";

          const confirm = await Swal.fire({
            icon: "warning",
            title: "¿Eliminar este item?",
            text: `¿Seguro que deseas eliminar ${producto}?`,
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
          });

          if (!confirm.isConfirmed) return;

          fila.remove();
          recalcularTotal();

          Swal.fire({
            icon: "success",
            title: "Item eliminado",
            timer: 1000,
            showConfirmButton: false,
          });
        });


      });
    </script>

    <!-- VALIDAR BOTON DE IMPRIMIR -->
    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const formOrden = document.getElementById("formOrden");
        const btnGuardar = formOrden.querySelector(".btn.btn-success[type='submit']");
        const btnAgregar = document.getElementById("btnAgregarItem");
        const tablaItems = document.querySelector("#tablaItems tbody");

        // 🔹 Función que habilita/deshabilita el botón de guardar
        function validarFormularioOrden() {
          const proveedor = document.getElementById("proveedor").value.trim();
          const rnc = document.getElementById("rnc").value.trim();
          const tieneItems = tablaItems.children.length > 0;

          btnGuardar.disabled = !(proveedor && rnc && tieneItems);
        }

        // 🔹 Escuchar cambios en inputs y en tabla
        ["proveedor", "rnc", "producto", "precio", "cantidad"].forEach(id => {
          document.getElementById(id).addEventListener("input", validarFormularioOrden);
        });
        btnAgregar.addEventListener("click", () => setTimeout(validarFormularioOrden, 100));
        tablaItems.addEventListener("click", () => setTimeout(validarFormularioOrden, 100));

      });
    </script>

    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const crearOrdenModal = document.getElementById("crearOrdenModal");
        const btnCrearOrden = document.querySelector(".btn-create");
        btnCrearOrden.addEventListener("click", async (e) => {
          e.preventDefault();

          Swal.fire({
            title: "Verificando NFC disponible...",
            didOpen: () => Swal.showLoading(),
            allowOutsideClick: false,
            allowEscapeKey: false
          });

          try {
            const res = await fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/orders/nfc_status.php");
            const data = await res.json();

            Swal.close();

            if (!data.status) {
              await Swal.fire({
                icon: "error",
                title: "No se puede crear la orden",
                text: data.msg
              });
              return;
            }

            // ⚠️ Mostrar toast si quedan pocos NFC
            if (data.alerta) {
              const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 4500,
                timerProgressBar: true,
                background: "#f8f3eb", // beige claro
                color: "#333",
                customClass: {
                  popup: "shadow-lg border border-success-subtle",
                  title: "fw-semibold"
                },
                didOpen: (toast) => {
                  toast.addEventListener("mouseenter", Swal.stopTimer);
                  toast.addEventListener("mouseleave", Swal.resumeTimer);
                }
              });

              Toast.fire({
                icon: "warning",
                iconColor: "#a6b91a", // verde oliva de Allura
                title: data.alerta
              });
            }


            const modal = bootstrap.Modal.getOrCreateInstance(crearOrdenModal);

            // 🔹 Cargar suplidores antes de abrir el modal
            if (typeof obtenerSuplidores === "function") {
              await obtenerSuplidores();
            }

            modal.show();
            const headerRight = crearOrdenModal.querySelector(".header-right");
            if (headerRight) {
              const strongs = headerRight.querySelectorAll("strong");
              if (strongs.length >= 2) {
                strongs[0].textContent = data.order_number || "—";
                strongs[1].textContent = data.nfc || "—";
              }
            }

          } catch (err) {
            Swal.close();
            await Swal.fire({
              icon: "error",
              title: "Error de conexión",
              text: "No se pudo validar el estado de los códigos NFC."
            });
          }
        });
      });
    </script>

    <!-- NFC -->
    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const modalCrear = document.getElementById("crearOrdenModal");
        const modalInstance = new bootstrap.Modal(modalCrear);
        const formOrden = document.getElementById("formOrden");
        const btnGuardar = formOrden.querySelector("button[type='submit']");
        const tituloModal = modalCrear.querySelector(".modal-title");
        const tablaItems = modalCrear.querySelector("#tablaItems tbody");
        const totalGeneral = modalCrear.querySelector("#totalGeneral");

        // 🧾 Abrir modal en modo edición (sin endpoint)
        document.body.addEventListener("click",async(e) => {
          const btnEditar = e.target.closest(".btnEditar");
          if (!btnEditar) return;

          const fila = btnEditar.closest("tr");
          const factura = fila.children[1].textContent.trim();
          const proveedor = fila.children[3].textContent.trim();
          const cantidad = fila.children[4].textContent.trim();
          const total = fila.children[6].textContent.replace("RD$", "").trim();

          // Rellenar datos en el modal
          document.getElementById("proveedor").value = proveedor;
          document.getElementById("rnc").value = fila.dataset.rnc || "";
          document.getElementById("telefono").value = fila.dataset.telefono || "";
          // 🔒 Bloquear campos del cliente en modo edición
          document.getElementById("proveedor").readOnly = true;
          document.getElementById("rnc").readOnly = true;
          document.getElementById("telefono").readOnly = true;
          document.getElementById("tipoProveedor").disabled = true;
          document.getElementById("selectSuplidorContainer").style.display = "none";


          totalGeneral.textContent = `RD$ ${parseFloat(total || 0).toFixed(1)}`;

          // 🔹 Limpiar tabla y total
          tablaItems.innerHTML = "";
          totalGeneral.textContent = `RD$ ${parseFloat(total || 0).toFixed(1)}`;


          // Cambiar estado del modal
          tituloModal.textContent = "Actualizar Orden de Compra";
          btnGuardar.textContent = "Actualizar Orden";
          btnGuardar.classList.remove("btn-success");
          btnGuardar.classList.add("btn-primary");
          btnGuardar.dataset.mode = "edit";
          btnGuardar.dataset.orderNumber = factura;

          // Agregar botón eliminar si no existe
          let btnEliminarOrden = formOrden.querySelector(".btnEliminarOrden");
          if (!btnEliminarOrden) {
            btnEliminarOrden = document.createElement("button");
            btnEliminarOrden.type = "button";
            btnEliminarOrden.className = "btn btn-danger btnEliminarOrden";
            btnEliminarOrden.innerHTML = '<i class="bi bi-trash"></i> Eliminar Orden';
            formOrden.querySelector(".d-flex.justify-content-center.gap-3").appendChild(btnEliminarOrden);
          }

          modalInstance.show();

          // 🔹 Cargar ítems de la orden
          try {
            const res = await fetch("<?php echo URL_BACKEND; ?>" +`/allura/allura-backend/api/orders/listar_items.php?order_id=${fila.dataset.orderId}`);
            const result = await res.json();

            if (result.status && Array.isArray(result.data)) {
              const items = result.data;
              tablaItems.innerHTML = "";
              let total = 0;

              // 🔹 Mostrar número de orden y NFC en el encabezado del modal
              const headerRight = document.querySelector("#crearOrdenModal .header-right");
              if (headerRight) {
                const strongs = headerRight.querySelectorAll("strong");
                if (strongs.length >= 2) {
                  strongs[0].textContent = items[0]?.order_number || "—";
                  strongs[1].textContent = items[0]?.nfc_code || "—";
                }
              }

              // 🔹 Rellenar tabla de ítems
              items.forEach(item => {
                const filaItem = document.createElement("tr");
                const subtotal = parseFloat(item.subtotal);
                total += subtotal;

                filaItem.dataset.total = subtotal;
                filaItem.innerHTML = `
                  <td>${item.product_name}</td>
                  <td>${item.unit || "—"}</td>
                  <td>RD$ ${parseFloat(item.price).toFixed(1)}</td>
                  <td>${parseFloat(item.quantity)}</td>
                  <td>RD$ ${subtotal.toFixed(1)}</td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger btnEliminarItem">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                `;
                tablaItems.appendChild(filaItem);
              });

              // 🔹 Mostrar total
              totalGeneral.textContent = `RD$ ${total.toFixed(1)}`;
            } else {
              console.warn("No se encontraron ítems para esta orden.");
              tablaItems.innerHTML = `<tr><td colspan="6" class="text-center text-muted">Sin productos registrados</td></tr>`;
            }
          } catch (err) {
            console.error("Error cargando ítems:", err);
            tablaItems.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Error al cargar ítems</td></tr>`;
          }


        });

        // 💾 Guardar actualización
        formOrden.addEventListener("submit", async (e) => {
          e.preventDefault();

          // Si está en modo creación
          if (!btnGuardar.dataset.mode || btnGuardar.dataset.mode === "create") {
            const proveedor = document.getElementById("proveedor").value.trim();
            const rnc = document.getElementById("rnc").value.trim();
            const telefono = document.getElementById("telefono").value.trim();
            const items = Array.from(tablaItems.querySelectorAll("tr")).map(fila => {
              const celdas = fila.querySelectorAll("td");
              return {
                producto: celdas[0].textContent.trim(),
                unidad: celdas[1].textContent.trim(),
                costo: celdas[2].textContent.replace("RD$", "").trim(),
                cantidad: celdas[3].textContent.trim(),
                total: celdas[4].textContent.replace("RD$", "").trim()
              };
            });

            if (!proveedor || !rnc || items.length === 0) {
              Swal.fire({
                icon: "error",
                title: "Faltan datos",
                text: "Debe completar todos los campos y agregar al menos un producto."
              });
              return;
            }

            const headerRight = document.querySelector("#crearOrdenModal .header-right");
            const orderNumber = headerRight?.querySelector("p:nth-child(1) strong")?.textContent?.trim() || "—";
            const nfcCode = headerRight?.querySelector("p:nth-child(2) strong")?.textContent?.trim() || "—";
            const tipo_cliente = document.getElementById("tipoProveedor").value;

            try {
              Swal.fire({ title: "Guardando orden...", didOpen: () => Swal.showLoading() });

              const res = await fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/orders/guardar_orden.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ order_number: orderNumber, nfc_code: nfcCode, proveedor, rnc, telefono, items ,tipo_cliente})
              });

              const result = await res.json();
              if (!result.status) throw new Error(result.msg || "Error al guardar la orden");
              Swal.close();

              // Generar PDF
              const pdfRes = await fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/orders/generar_pdf.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ order_number: orderNumber, nfc_code: nfcCode, proveedor, rnc, telefono, fecha: new Date().toLocaleDateString('es-DO'), items })
              });

              const blob = await pdfRes.blob();
              const url = window.URL.createObjectURL(blob);
              window.open(url, "_blank");

              Swal.fire({
                icon: "success",
                title: "Orden creada e impresa correctamente",
                showConfirmButton: false,
                timer: 2500
              });

              // Marcar NFC usado
              await fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/orders/marcar_nfc.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ nfc_code: nfcCode })
              });

              const modal = bootstrap.Modal.getInstance(document.getElementById("crearOrdenModal"));
              modal.hide();
              document.dispatchEvent(new Event("ordenCreada"));
              listarPendientes();
            } catch (err) {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: err.message || "No se pudo completar el proceso."
              });
            }
            return; // ⛔ Evita seguir al bloque de edición
          }

          // Solo si está en modo edición

          if (btnGuardar.dataset.mode === "edit"){
            const order_number = btnGuardar.dataset.orderNumber;
            const proveedor = document.getElementById("proveedor").value.trim();
            const rnc = document.getElementById("rnc").value.trim();
            const telefono = document.getElementById("telefono").value.trim();

            // Capturamos ítems actuales del modal
            const items = Array.from(document.querySelectorAll("#tablaItems tbody tr"))
              .map(tr => {
                const tds = tr.querySelectorAll("td");
                const costo = parseFloat(tds[2].textContent.replace("RD$", "").trim());
                const cantidad = parseFloat(tds[3].textContent.trim());
                const total = parseFloat(tds[4].textContent.replace("RD$", "").trim());
                if (!tds[0] || isNaN(costo) || isNaN(cantidad) || cantidad <= 0) return null;
                return {
                  producto: tds[0].textContent.trim(),
                  unidad: tds[1].textContent.trim() || "N/A",
                  costo,
                  cantidad,
                  total: isNaN(total) ? costo * cantidad : total
                };
              })
              .filter(i => i !== null);

            if (!proveedor || !rnc) {
              Swal.fire("Campos incompletos", "Debe completar los datos del proveedor.", "warning");
              return;
            }

            if (items.length === 0) {
              Swal.fire({
                icon: "warning",
                title: "Sin productos",
                text: "Debe agregar al menos un producto válido antes de actualizar la orden."
              });
              return;
            }

            try {
              Swal.fire({ title: "Actualizando orden...", didOpen: () => Swal.showLoading() });

              // 🔹 Enviar actualización al backend
              const res = await fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/orders/actualizar_orden.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ order_number, proveedor, rnc, telefono, items })
              });

              const data = await res.json();
              Swal.close();

              if (!data.status) throw new Error(data.msg || "Error al actualizar la orden");

              // ✅ Confirmación
              await Swal.fire({
                icon: "success",
                title: "Orden actualizada correctamente",
                timer: 1500,
                showConfirmButton: false
              });
              // 🔹 Regenerar PDF actualizado
              const pdfUpdate = await fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/orders/actualizar_pdf.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ order_number, proveedor, rnc, telefono, fecha: new Date().toLocaleDateString('es-DO'), items })
              });

              const blobPdf = await pdfUpdate.blob();
              const urlPdf = window.URL.createObjectURL(blobPdf);
              window.open(urlPdf, "_blank");


              // 🔄 Refrescar listado principal
              document.dispatchEvent(new Event("ordenCreada"));
              listarPendientes();

              // 🧹 Cerrar y limpiar modal para reiniciar flujo
              const modalInstance = bootstrap.Modal.getInstance(modalCrear);
              modalInstance.hide();

              // Reiniciar formulario y estado del modal
              formOrden.reset();
              tablaItems.innerHTML = "";
              totalGeneral.textContent = "RD$ 0.00";
              btnGuardar.textContent = "Guardar Orden";
              btnGuardar.classList.remove("btn-primary");
              btnGuardar.classList.add("btn-success");
              btnGuardar.dataset.mode = "create";
              btnGuardar.removeAttribute("data-order-number");

              const eliminarBtn = formOrden.querySelector(".btnEliminarOrden");
              if (eliminarBtn) eliminarBtn.remove();

              const tituloModal = modalCrear.querySelector(".modal-title");
              if (tituloModal) tituloModal.textContent = "Nueva Orden de Compra";

              const headerRight = modalCrear.querySelector(".header-right");
              if (headerRight) {
                const strongs = headerRight.querySelectorAll("strong");
                if (strongs.length >= 2) {
                  strongs[0].textContent = "—";
                  strongs[1].textContent = "—";
                }
              }

            } catch (err) {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: err.message || "No se pudo actualizar la orden."
              });
            }
          }

          
        });

        // 🗑️ Eliminar orden
        document.body.addEventListener("click", async (e) => {
          const btn = e.target.closest(".btnEliminarOrden");
          if (!btn) return;

          const order_number = btnGuardar.dataset.orderNumber;

          const confirm = await Swal.fire({
            icon: "warning",
            title: "¿Eliminar esta orden?",
            text: "Esta acción no se puede deshacer.",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
          });

          if (!confirm.isConfirmed) return;

          Swal.fire({
            icon: "success",
            title: "Orden eliminada correctamente",
            timer: 1500,
            showConfirmButton: false
          });

          modalInstance.hide();
          document.dispatchEvent(new Event("ordenCreada"));
        });

        // 🧹 Limpiar modal al cerrarlo
        modalCrear.addEventListener("hidden.bs.modal", () => {
          formOrden.reset();
          tablaItems.innerHTML = "";
          totalGeneral.textContent = "RD$ 0.00";
          btnGuardar.textContent = "Guardar Orden";
          btnGuardar.classList.remove("btn-primary");
          btnGuardar.classList.add("btn-success");
          btnGuardar.dataset.mode = "create";
          btnGuardar.removeAttribute("data-order-number");
          const eliminarBtn = formOrden.querySelector(".btnEliminarOrden");
          if (eliminarBtn) eliminarBtn.remove();
          tituloModal.textContent = "Nueva Orden de Compra";
          // 🔓 Restaurar estado editable al cerrar modal
          document.getElementById("proveedor").readOnly = false;
          document.getElementById("rnc").readOnly = false;
          document.getElementById("telefono").readOnly = false;
          document.getElementById("tipoProveedor").disabled = false;

          // 🧹 Limpiar estado de suplidores y productos
          const selectSuplidorContainer = document.getElementById("selectSuplidorContainer");
          if (selectSuplidorContainer) selectSuplidorContainer.style.display = "none";

          const selectSuplidor = document.getElementById("selectSuplidor");
          if (selectSuplidor) {
            selectSuplidor.innerHTML = '<option value="">-- Selecciona un Suplidor --</option>';
          }

          const productosDelSuplidor = document.getElementById("productosDelSuplidor");
          if (productosDelSuplidor) productosDelSuplidor.remove();

          const tipoProveedor = document.getElementById("tipoProveedor");
          if (tipoProveedor) tipoProveedor.value = "nuevo";

          ["proveedor", "rnc", "telefono", "producto", "precio", "cantidad"].forEach(id => {
            const input = document.getElementById(id);
            if (input) {
              input.value = "";
              input.removeAttribute("readonly");
            }
          });

        });
      });
    </script>

    <!-- ELIMINAR --> 
    <script>
      document.addEventListener("DOMContentLoaded", () => {
        document.body.addEventListener("click", async (e) => {
          const btn = e.target.closest(".btnEliminar");
          if (!btn) return;

          const fila = btn.closest("tr");
          const order_number = fila.children[1]?.textContent?.trim();
          if (!order_number) {
            Swal.fire("Error", "No se pudo identificar la orden seleccionada.", "error");
            return;
          }

          const confirm = await Swal.fire({
            title: "¿Eliminar esta orden?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
          });

          if (!confirm.isConfirmed) return;

          try {
            Swal.fire({ title: "Eliminando orden...", didOpen: () => Swal.showLoading() });

            const res = await fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/orders/eliminar_orden.php", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ order_number })
            });

            const data = await res.json();
            Swal.close();

            if (!data.status) throw new Error(data.msg);

            Swal.fire({
              icon: "success",
              title: "Orden eliminada correctamente",
              showConfirmButton: false,
              timer: 1500
            });

            document.dispatchEvent(new Event("ordenCreada"));
            listarPendientes(); // 🔄 refresca tabla

          } catch (err) {
            Swal.fire("Error", err.message || "No se pudo eliminar la orden.", "error");
          }
        });
      });
    </script>

    <script>
      document.addEventListener('DOMContentLoaded', async () => {
        const tipoProveedor = document.getElementById('tipoProveedor');
        const proveedorInput = document.getElementById('proveedor');
        const rncInput = document.getElementById('rnc');
        const telefonoInput = document.getElementById('telefono');
        const selectSuplidorContainer = document.getElementById('selectSuplidorContainer');
        const selectSuplidor = document.getElementById('selectSuplidor');
        const productoInput = document.getElementById('producto');

        let supliers = [];

        // 🔹 Cargar suplidores desde el backend
        async function obtenerSuplidores() {
          try {
            selectSuplidor.innerHTML = '<option>Cargando suplidores...</option>';
            const res = await fetch("<?php echo URL_BACKEND; ?>" +"/allura/allura-backend/api/suppliers/list.php");
            const data = await res.json();

            if (!data.status || !Array.isArray(data.data)) {
              throw new Error(data.msg || 'No se pudieron obtener los suplidores');
            }

            supliers = data.data;
            llenarSelectSuplidores();
          } catch (err) {
            console.error('Error cargando suplidores:', err);
            selectSuplidor.innerHTML = '<option value="">Error al cargar suplidores</option>';
          }
        }

        // 🔹 Llenar select con suplidores
        function llenarSelectSuplidores() {
          selectSuplidor.innerHTML = '<option value="">-- Selecciona un suplidor --</option>';
          supliers.forEach(s => {
            const option = document.createElement('option');
            const id = s.id ?? s.ruc;
            option.value = id;
            option.textContent = s.name || s.commercial_name || 'Sin nombre';
            selectSuplidor.appendChild(option);
          });
        }

        // 🔹 Crear select de productos del suplidor
        function crearSelectProductos(productos) {
          eliminarSelectProductos();

          const container = document.createElement('div');
          container.id = 'productosDelSuplidor';
          container.className = 'mb-3';
          container.innerHTML = `
            <label class="form-label">Productos del Suplidor</label>
            <div class="d-flex gap-2 align-items-center">
              <select class="form-select flex-grow-1" id="selectProductoSuplidor">
                <option value="">-- Selecciona un producto --</option>
                ${productos.map(p => `<option value="${p}">${p}</option>`).join('')}
              </select>
              <button type="button" class="btn btn-outline-success btn-sm" id="btnAgregarProductoSuplidor" title="Agregar producto">
                <i class="bi bi-plus-lg"></i>
              </button>
            </div>
          `;

          const productoCol = productoInput.closest('.col-md-4');
          productoCol.parentNode.insertBefore(container, productoCol);

          document.getElementById('btnAgregarProductoSuplidor').addEventListener('click', () => {
            const selected = document.getElementById('selectProductoSuplidor').value.trim();
            if (selected) productoInput.value = selected;
          });
        }

        // 🔹 Eliminar el select de productos si existe
        function eliminarSelectProductos() {
          const existente = document.getElementById('productosDelSuplidor');
          if (existente) existente.remove();
        }

        // 🔹 Cambio tipo de proveedor
        tipoProveedor.addEventListener('change', async () => {
          const tipo = tipoProveedor.value;
          if (tipo === 'registrado') {
            selectSuplidorContainer.style.display = 'block';
            proveedorInput.readOnly = true;
            rncInput.readOnly = true;
            telefonoInput.readOnly = true;
            proveedorInput.value = '';
            rncInput.value = '';
            telefonoInput.value = '';
            eliminarSelectProductos();
            await obtenerSuplidores(); // Cargar desde backend al abrir
          } else {
            selectSuplidorContainer.style.display = 'none';
            proveedorInput.readOnly = false;
            rncInput.readOnly = false;
            telefonoInput.readOnly = false;
            proveedorInput.value = '';
            rncInput.value = '';
            telefonoInput.value = '';
            eliminarSelectProductos();
          }
        });

        // 🔹 Seleccionar un suplidor
        selectSuplidor.addEventListener('change', () => {
          const idSeleccionado = selectSuplidor.value;
          const suplidor = supliers.find(s => (s.id ?? s.ruc) == idSeleccionado);

          if (suplidor) {
            proveedorInput.value = suplidor.name || suplidor.commercial_name || '';
            rncInput.value = suplidor.ruc || '';
            telefonoInput.value = suplidor.phone || '';

            let productos = [];
            if (Array.isArray(suplidor.products) && suplidor.products.length > 0) {
              productos = suplidor.products;
            } else if (typeof suplidor.products === "string" && suplidor.products.trim() !== "") {
              // Si viene como texto separado por comas
              productos = suplidor.products.split(",").map(p => p.trim());
            }


            if (productos.length > 0) {
              crearSelectProductos(productos);
            } else {
              eliminarSelectProductos();
            }
          } else {
            proveedorInput.value = '';
            rncInput.value = '';
            telefonoInput.value = '';
            eliminarSelectProductos();
          }
        });

        // 🔸 Exportar función global
        window.obtenerSuplidores = obtenerSuplidores;
      });
    </script>

  </body>
</html>
