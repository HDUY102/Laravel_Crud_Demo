<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sản Phẩm - Laravel API</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 text-slate-800 font-sans antialiased">

    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Quản Lý Sản Phẩm</h1>
                <p class="text-slate-500 text-sm mt-1">Giao diện CRUD kết nối trực tiếp tới Laravel RESTful API</p>
            </div>
            <button onclick="openModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2.5 rounded-lg transition-all flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-plus"></i> Thêm sản phẩm
            </button>
        </div>

        <!-- Notification -->
        <div id="toast" class="hidden mb-6 p-4 rounded-lg text-sm font-medium transition-all"></div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-4">Hình ảnh</th>
                            <th class="py-3.5 px-4">Tên sản phẩm</th>
                            <th class="py-3.5 px-4">Giá bán</th>
                            <th class="py-3.5 px-4">Trạng thái</th>
                            <th class="py-3.5 px-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="product-list" class="divide-y divide-slate-200 text-sm">
                        <!-- Loading State -->
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-circle-notch fa-spin text-2xl"></i>
                                <p class="mt-2 text-xs">Đang tải dữ liệu...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="pagination" class="px-4 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between"></div>
        </div>
    </div>

    <!-- Modal Form (Thêm / Sửa) -->
    <div id="modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 id="modal-title" class="font-bold text-lg text-slate-800">Thêm sản phẩm mới</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <form id="product-form" onsubmit="handleSubmit(event)" class="p-6 space-y-4">
                <input type="hidden" id="product-id">

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Tên sản phẩm *</label>
                    <input type="text" id="nameProduct" required placeholder="Nhập tên sản phẩm" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Giá (VNĐ) *</label>
                    <input type="number" id="price" required min="0" placeholder="0" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Hình ảnh sản phẩm</label>
                    <input type="file" id="image" accept="image/*" onchange="previewImage(event)" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-300 rounded-lg">
                
                    <!-- Frame Preview Image -->
                    <div id="image-preview-container" class="mt-2 hidden">
                        <img id="image-preview" src="" alt="Preview" class="w-16 h-16 object-cover rounded-lg border border-slate-200">
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Trạng thái</label>
                    <select id="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="1">Đang bán</option>
                        <option value="0">Ẩn / Tạm ngưng</option>
                    </select>
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg">Hủy</button>
                    <button type="submit" id="btn-submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg">Lưu lại</button>
                </div>
            </form>
        </div>
    </div>

    <script>
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

                if (!result.success) throw new Error(result.message);

                const { data: products, links } = result.data;

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
                showToast('Không thể kết nối tới API!', 'danger');
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

            // Dùng FormData để gửi được File
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
                // Laravel giải mã multipart/form-data tốt nhất khi dùng POST + _method PUT
                formData.append('_method', 'PUT');
            }

            try {
                const res = await fetch(url, {
                    method: 'POST', // Luôn dùng POST khi upload file với FormData
                    headers: {
                        'Accept': 'application/json'
                        // Tuyệt đối không để Content-Type ở đây, fetch tự set multipart/form-data
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

                // Reset file input & hiển thị preview ảnh hiện tại nếu có
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
    </script>
</body>
</html>