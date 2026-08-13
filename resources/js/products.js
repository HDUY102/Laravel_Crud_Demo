const API_URL = '/api/products';
let currentPage = 1;

document.addEventListener('DOMContentLoaded', () => {
    fetchProducts();
});

// Preview ảnh trước khi upload
function previewImage(event) {
    const file = event.target.files[0];
    const container = document.getElementById('image-preview-container');
    const img = document.getElementById('image-preview');
    if (file) {
        img.src = URL.createObjectURL(file);
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
}

// 1. Fetch Danh sách sản phẩm
async function fetchProducts(page = 1) {
    currentPage = page;
    const tbody = document.getElementById('product-list');
    
    try {
        const res = await fetch(`${API_URL}?page=${page}`, {
            headers: { 'Accept': 'application/json' }
        });
        
        const result = await res.json();

        if (!res.ok || !result.success) {
            throw new Error(result.message || 'Lỗi tải danh sách sản phẩm');
        }

        const products = result.data.data || [];
        const links = result.data.links || [];

        if (products.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="py-8 text-center text-slate-400">
                        Chưa có sản phẩm nào trong cơ sở dữ liệu.
                    </td>
                </tr>`;
            document.getElementById('pagination').innerHTML = '';
            return;
        }

        tbody.innerHTML = products.map(item => `
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="py-3 px-4">
                    <img src="${item.image_url || item.image || 'https://via.placeholder.com/60'}" 
                        alt="${item.nameProduct}" 
                        class="w-12 h-12 object-cover rounded-lg border border-slate-200"
                        onerror="this.src='https://via.placeholder.com/60?text=No+Img'">
                </td>
                <td class="py-3 px-4 font-medium text-slate-900">${escapeHtml(item.nameProduct)}</td>
                <td class="py-3 px-4 font-semibold text-indigo-600">${Number(item.price).toLocaleString('vi-VN')} đ</td>
                <td class="py-3 px-4">
                    ${item.status == 1 
                        ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Đang bán</span>'
                        : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">Ẩn</span>'}
                </td>
                <td class="py-3 px-4 text-right space-x-2">
                    <button onclick="editProduct('${item.id}')" class="text-indigo-600 hover:text-indigo-900 font-medium p-1">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </button>
                    <button onclick="deleteProduct('${item.id}')" class="text-rose-600 hover:text-rose-900 font-medium p-1">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        renderPagination(links);

    } catch (err) {
        showToast(err.message || 'Không thể kết nối tới API!', 'danger');
    }
}

// 2. Render Nút Phân trang
function renderPagination(links) {
    const nav = document.getElementById('pagination');
    if (!links || links.length <= 3) {
        nav.innerHTML = '';
        return;
    }

    nav.innerHTML = links.map(link => {
        if (!link.url) {
            return `<span class="px-3 py-1 text-xs text-slate-400 border border-slate-200 rounded">${link.label}</span>`;
        }
        const pageNum = new URL(link.url).searchParams.get('page');
        const isActive = link.active 
            ? 'bg-indigo-600 text-white font-bold border-indigo-600' 
            : 'bg-white text-slate-600 hover:bg-slate-100 border-slate-200';
        
        return `<button onclick="fetchProducts(${pageNum})" class="px-3 py-1 text-xs border rounded transition-all ${isActive}">${link.label}</button>`;
    }).join('');
}

// 3. Xử lý Thêm / Sửa
async function handleSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('product-id').value;
    const isUpdate = Boolean(id);

    const formData = new FormData();
    formData.append('nameProduct', document.getElementById('nameProduct').value);
    formData.append('price', document.getElementById('price').value);
    formData.append('status', document.getElementById('status').value);

    const imageFile = document.getElementById('image').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    }

    let url = API_URL;
    if (isUpdate) {
        url = `${API_URL}/${id}`;
        formData.append('_method', 'PUT');
    }

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            },
            body: formData
        });

        const result = await res.json();

        if (!res.ok) {
            const msg = result.message || Object.values(result.errors || {}).flat().join(', ');
            throw new Error(msg);
        }

        showToast(isUpdate ? 'Cập nhật thành công!' : 'Thêm mới thành công!', 'success');
        closeModal();
        fetchProducts(currentPage);

    } catch (err) {
        showToast(err.message, 'danger');
    }
}

// 4. Lấy chi tiết & Đổ vào Modal sửa
async function editProduct(id) {
    try {
        const res = await fetch(`${API_URL}/${id}`, {
            headers: { 'Accept': 'application/json' }
        });
        const result = await res.json();
        
        if (!result.success) throw new Error(result.message);

        const data = result.data;
        document.getElementById('product-id').value = data.id;
        document.getElementById('nameProduct').value = data.nameProduct;
        document.getElementById('price').value = data.price;
        document.getElementById('status').value = data.status;

        document.getElementById('image').value = '';
        const previewContainer = document.getElementById('image-preview-container');
        const previewImg = document.getElementById('image-preview');
        
        if (data.image_url) {
            previewImg.src = data.image_url;
            previewContainer.classList.remove('hidden');
        } else {
            previewContainer.classList.add('hidden');
        }

        document.getElementById('modal-title').innerText = 'Chỉnh sửa sản phẩm';
        openModal();
    } catch (err) {
        showToast(err.message, 'danger');
    }
}

// 5. Xóa sản phẩm
async function deleteProduct(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) return;

    try {
        const res = await fetch(`${API_URL}/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json' }
        });
        const result = await res.json();

        if (!res.ok) throw new Error(result.message);

        showToast('Đã xóa sản phẩm thành công!', 'success');
        fetchProducts(currentPage);
    } catch (err) {
        showToast(err.message, 'danger');
    }
}

// Modal Controls
function openModal() {
    document.getElementById('modal').classList.remove('hidden');
    document.getElementById('modal').classList.add('flex');
}

function closeModal() {
    document.getElementById('modal').classList.add('hidden');
    document.getElementById('modal').classList.remove('flex');
    document.getElementById('product-form').reset();
    document.getElementById('product-id').value = '';
    document.getElementById('image-preview-container').classList.add('hidden');
    document.getElementById('modal-title').innerText = 'Thêm sản phẩm mới';
}

// Helper Toast
function showToast(msg, type = 'success') {
    const toast = document.getElementById('toast');
    toast.className = `mb-6 p-4 rounded-lg text-sm font-medium ${
        type === 'success' 
            ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' 
            : 'bg-rose-50 text-rose-800 border border-rose-200'
    }`;
    toast.innerText = msg;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 4000);
}

function escapeHtml(str) {
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
}

window.openModal = openModal;
window.closeModal = closeModal;
window.handleSubmit = handleSubmit;
window.editProduct = editProduct;
window.deleteProduct = deleteProduct;
window.previewImage = previewImage;
window.fetchProducts = fetchProducts;