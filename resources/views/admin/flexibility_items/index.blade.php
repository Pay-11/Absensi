@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 font-weight-bold text-gray-800">Flexibility Items (Toko Points)</h1>
        <button class="btn btn-primary" onclick="openCreateModal()">
            + Tambah Item Toko
        </button>
    </div>

    <!-- Tabel Data -->
    <div class="card shadow mb-4 border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="dataTable" width="100%">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Item</th>
                            <th>Cost (Point)</th>
                            <th>Tipe Voucher</th>
                            <th>Batas Keterlambatan</th>
                            <th>Stock Limit / Bulan</th>
                            <th class="text-center" width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- Dimuat lewat AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="dataForm" onsubmit="saveData(event)">
          <div class="modal-header">
            <h5 class="modal-title font-weight-bold" id="modalTitle">Tambah Flexibility Item</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <input type="hidden" id="item_id">
              
              <div class="row">
                  <div class="col-md-6 mb-3">
                      <label class="form-label fw-bold">Nama Item/Voucher</label>
                      <input type="text" id="item_name" class="form-control" required placeholder="Contoh: Bebas Telat 15 Menit">
                      <div class="invalid-feedback" id="err_item_name"></div>
                  </div>
                  <div class="col-md-6 mb-3">
                      <label class="form-label fw-bold text-warning">Point Cost (Harga Poin)</label>
                      <input type="number" id="point_cost" class="form-control" min="1" required placeholder="50">
                      <div class="invalid-feedback" id="err_point_cost"></div>
                  </div>
                  <div class="col-md-6 mb-3">
                      <label class="form-label fw-bold">Tipe Kegunaan</label>
                      <select id="type" class="form-select" required onchange="toggleLateField()">
                          <option value="LATE">Toleransi Keterlambatan (LATE)</option>
                          <option value="ALPHA">Anulir Alpha/Tidak Hadir (ALPHA)</option>
                      </select>
                      <div class="invalid-feedback" id="err_type"></div>
                  </div>
                  <div class="col-md-6 mb-3 late-field">
                      <label class="form-label fw-bold">Maksimal Toleransi (Menit)</label>
                      <input type="number" id="max_late_minutes" class="form-control" min="1">
                      <div class="invalid-feedback" id="err_max_late_minutes"></div>
                  </div>
                  <div class="col-md-12 mb-3">
                      <label class="form-label fw-bold">Stock Limit / Bulan</label>
                      <input type="number" id="stock_limit" class="form-control" min="1" placeholder="Kosongkan jika unlimited">
                      <div class="invalid-feedback" id="err_stock_limit"></div>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Letakkan Script AJAX -->
<script>
    const API_URL = "{{ route('admin.flexibility-items.index') }}";
    const csrfToken = "{{ csrf_token() }}";
    let modalInstance = null;
    
    document.addEventListener("DOMContentLoaded", () => {
        // Init Bootstrap modal if using bs5
        modalInstance = new bootstrap.Modal(document.getElementById('formModal'));
        loadData();
    });

    function toggleLateField() {
        const type = document.getElementById('type').value;
        const container = document.querySelector('.late-field');
        if(type === 'LATE') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }

    // LOAD DATA
    function loadData() {
        fetch(API_URL, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(res => res.json())
        .then(res => {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';
            res.data.forEach((item) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="fw-bold">${item.item_name}</td>
                    <td><span class="badge bg-warning text-dark px-3 mt-1">💎 ${item.point_cost} pt</span></td>
                    <td><span class="badge ${item.type === 'LATE' ? 'bg-primary' : 'bg-danger'}">${item.type}</span></td>
                    <td>${item.type === 'LATE' ? item.max_late_minutes + ' Menit' : 'Anulir Alpha'}</td>
                    <td>${item.stock_limit ? item.stock_limit + ' Pcs' : 'Unlimited'}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-warning" onclick="openEditModal(${item.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteData(${item.id})">Hapus</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        });
    }

    // OPEN CREATE
    function openCreateModal() {
        document.getElementById('dataForm').reset();
        document.getElementById('item_id').value = '';
        document.getElementById('modalTitle').innerText = 'Tambah Item Baru';
        clearErrors();
        toggleLateField();
        modalInstance.show();
    }

    // OPEN EDIT
    function openEditModal(id) {
        clearErrors();
        fetch(API_URL + '/' + id, { headers: { "X-Requested-With": "XMLHttpRequest" } })
        .then(res => res.json())
        .then(res => {
            const data = res.data;
            document.getElementById('item_id').value = data.id;
            document.getElementById('item_name').value = data.item_name;
            document.getElementById('point_cost').value = data.point_cost;
            document.getElementById('type').value = data.type;
            document.getElementById('max_late_minutes').value = data.max_late_minutes;
            document.getElementById('stock_limit').value = data.stock_limit;
            
            document.getElementById('modalTitle').innerText = 'Edit Flexibility Item';
            toggleLateField();
            modalInstance.show();
        });
    }

    // SAVE DATA (Create/Edit)
    function saveData(e) {
        e.preventDefault();
        clearErrors();
        const id = document.getElementById('item_id').value;
        const url = id ? `${API_URL}/${id}` : API_URL;
        const method = id ? 'PUT' : 'POST';
        
        const payload = {
            item_name: document.getElementById('item_name').value,
            point_cost: document.getElementById('point_cost').value,
            type: document.getElementById('type').value,
            max_late_minutes: document.getElementById('max_late_minutes').value,
            stock_limit: document.getElementById('stock_limit').value
        };

        fetch(url, {
            method: method,
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const data = await res.json();
            if(!res.ok) {
                if(res.status === 422) showErrors(data.errors);
                else alert(data.message || 'Terjadi kesalahan sistem');
                throw new Error('Validation failed');
            }
            return data;
        })
        .then(data => {
            modalInstance.hide();
            alert(data.message);
            loadData();
        }).catch(err => console.log(err));
    }

    // DELETE DATA
    function deleteData(id) {
        if(confirm('Yakin ingin menghapus item ini dari toko?')) {
            fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    loadData();
                } else {
                    alert(data.message);
                }
            });
        }
    }

    // CLEAR & SHOW ERRORS
    function clearErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');
    }
    function showErrors(errors) {
        for (const [key, msgs] of Object.entries(errors)) {
            const input = document.getElementById(key);
            const errDiv = document.getElementById(`err_${key}`);
            if(input && errDiv) {
                input.classList.add('is-invalid');
                errDiv.innerText = msgs[0];
            }
        }
    }
</script>
@endsection
