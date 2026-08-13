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
    @vite(['resources/js/products.js'])
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
            <div id="pagination" class="px-4 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-center gap-1.5"></div>
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
</body>
</html>