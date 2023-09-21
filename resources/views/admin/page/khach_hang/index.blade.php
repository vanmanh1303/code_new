@extends('admin.share.master')
@section('noi_dung')
    <div class="row" id="app">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Danh Sách Khách Hàng
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="text-center text-nowrap align-middle">
                                    <th style="width: 10px;">#</th>
                                    <th>Họ và tên</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Giới tính</th>
                                    <th>Tình trạng</th>
                                    {{-- <th>Thao tác</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(v, key) in listKhachHang">
                                    <tr class="align-middle text-nowrap">
                                        <th class="text-center" style="width: 10px;">@{{key+1}}</th>
                                        <td>@{{v.ho_va_ten}}</td>
                                        <td>@{{v.email}}</td>
                                        <td>@{{v.so_dien_thoai}}</td>
                                        <td class="text-center">@{{v.gioi_tinh == 1?"Nữ":v.gioi_tinh == 0?"Nam":"Khác"}}</td>
                                        <td class="text-center">@{{v.loai_tai_khoan ==1?"Đã kích hoạt":"Chưa kích hoạt"}}</td>
                                        {{-- <td class="text-center">
                                            kk
                                        </td> --}}
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        new Vue({
            el: '#app',
            data: {
                listKhachHang: [],
            },
            created() {
                this.loadData();
            },
            methods: {
                loadData() {
                    axios
                        .get('/admin/khach-hang/data')
                        .then((res) => {
                            this.listKhachHang = res.data.data;
                        });
                },
            },
        });
    </script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.19.1/ckeditor.js"></script>
<script>
    var route_prefix = "/laravel-filemanager";
</script>
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
    $("#lfm").filemanager('image', {prefix : route_prefix});
    $("#lfm_update").filemanager('image', {prefix : route_prefix});
</script> --}}
@endsection
