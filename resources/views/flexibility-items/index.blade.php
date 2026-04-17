@extends('layouts.app')

@section('title', 'Toko Poin (Flex Items)')

@section('content')
<div class="container-xxl py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Item Toko (Flexibility)</h5>
            <button class="btn btn-primary btn-sm" onclick="openCreateModal()">+ Tambah Item</button>
        </div>
        <div class="card-body">
            <!-- Alert Placeholder -->
            <div id="alertContainer"></div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>NAMA ITEM</th>
                            <th>HARGA (POIN)</th>
                            <th>TIPE VOUCHER</th>
                            <th>BATAS TOLERANSI</th>
                            <th>STOCK (BULAN)</th>
                            <th width="150" class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="6" class="text-center">Loading data...</td></tr>
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
            <h5 class="modal-title font-weight-bold" id="modalTitle">Tambah Item</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <input type="hidden" id="item_id">
              
              <div class="row g-3">
                  <div class="col-md-6">
                      <label class="form-label fw-bold">Nama Voucher/Item</label>
                      <input type="text" id="item_name" class="form-control" required placeholder="Contoh: Bebas Telat 15 Men}">
                      <div class="invalid-feedback" id="err_item_name"></div>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label fw-bold text-warning">Point Cost (Harga)</label>
                      <input type="number" id="point_cost" class="form-control" required min="1">
                      <div class="invalid-feedback" id="err_point_cost"></div>
                  </div>
                  <div class="col-md-12">
                      <label class="form-label fw-bold">Tipe Kegunaan</label>
                      <select id="type" class="form-select" required onchange="toggleLateField()">
                          <option value="LATE">Toleransi Keterlambatan (LATE)</option>
                          <option value="ALPHA">Anulir Status Alpha (ALPHA)</option>
                      </select>
                      <div class="invalid-feedback" id="err_type"></div>
                  </div>
                  <div class="col-md-6 late-field">
                      <label class="form-label fw-bold">Maks Toleransi (Menit)</label>
                      <input type="number" id="max_late_minutes" class="form-control" min="1">
                      <div class="invalid-feedback" id="err_max_late_minutes"></div>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label fw-bold">Stock Limit</label>
                      <input type="number" id="stock_limit" class="form-control" min="1" placeholder="Kosong = Unlimited">
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

<script>
    const DATA_URL = "{{ route('flexibility-items.data') }}";
    const BASE_URL = "{{ url('admin/flexibility-items') }}"; 
    const csrfToken = "{{ csrf_token() }}";
    let modalInstance = null;
    
    document.addEventListener("DOMContentLoaded", () => {
        const modalEl = document.getElementById('formModal');
        if(typeof bootstrap !== 'undefined') {
            modalInstance = new bootstrap.Modal(modalEl);
        }
        loadData();
    });

    function showAlert(msg, type="success") {
        const container = document.getElementById('alertContainer');
        container.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${msg}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }

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
        fetch(DATA_URL, { headers: { "Accept": "application/json" } })
        .then(res => res.json())
        .then(res => {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';
            if(res.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Belum ada item di toko.</td></tr>';
                return;
            }
            res.forEach((item) => {
                let badgeType = item.type === 'LATE' 
                  ? `<span class="badge bg-primary">LATE</span>` 
                  : `<span class="badge bg-danger">ALPHA</span>`;
                
                let limit = item.type === 'LATE' 
                  ? `${item.max_late_minutes} Menit` 
                  : `-`;

                const tr = `<tr>
                    <td class="fw-bold">${item.item_name}</td>
                    <td><span class="badge border border-warning text-warning px-2">💎 ${item.point_cost} pts</span></td>
                    <td>${badgeType}</td>
                    <td>${limit}</td>
                    <td>${item.stock_limit ? item.stock_limit + ' Pcs' : 'Unlimited'}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-warning" onclick="openEditModal(${item.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteData(${item.id})">Del</button>
                    </td>
                </tr>`;
                tbody.insertAdjacentHTML('beforeend', tr);
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
        if(modalInstance) modalInstance.show();
    }

    // OPEN EDIT
    function openEditModal(id) {
        clearErrors();
        fetch(BASE_URL + '/' + id, { headers: { "Accept": "application/json" } })
        .then(res => res.json())
        .then(data => {
            document.getElementById('item_id').value = data.id;
            document.getElementById('item_name').value = data.item_name;
            document.getElementById('point_cost').value = data.point_cost;
            document.getElementById('type').value = data.type;
            document.getElementById('max_late_minutes').value = data.max_late_minutes;
            document.getElementById('stock_limit').value = data.stock_limit;
            
            document.getElementById('modalTitle').innerText = 'Edit Item';
            toggleLateField();
            if(modalInstance) modalInstance.show();
        });
    }

    // SAVE DATA (Create/Edit)
    function saveData(e) {
        e.preventDefault();
        clearErrors();
        const id = document.getElementById('item_id').value;
        const url = id ? `${BASE_URL}/${id}` : BASE_URL;
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
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest" 
            },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const data = await res.json();
            if(!res.ok) {
                if(res.status === 422) showErrors(data.errors);
                else showAlert(data.message || 'Terjadi kesalahan sistem', 'danger');
                throw new Error('Validation failed');
            }
            return data;
        })
        .then(data => {
            if(modalInstance) modalInstance.hide();
            showAlert('Berhasil menyimpan data item!', 'success');
            loadData();
        }).catch(err => console.log(err));
    }

    // DELETE DATA
    function deleteData(id) {
        if(confirm('Apakah Anda yakin ingin menghapus item ini?')) {
            fetch(`${BASE_URL}/${id}`, {
                method: 'DELETE',
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success || data.message) {
                    showAlert('Berhasil menghapus item!', 'success');
                    loadData();
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
