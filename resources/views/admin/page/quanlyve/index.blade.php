@extends('admin.share.master')
@section('noi_dung')
    <div id="app">
        <div class="row">
            <div class="col">

                <table class="table table-bordered">
                    <thead>
                        <tr class="text-center text-nowrap">
                            <th>Thời gian</th>
                            <th>Tuyến</th>
                            <th>Tài xế</th>
                            <th>Biển số xe</th>
                            <th>Tên ghế</th>
                            <th>Tên khách hàng</th>
                            <th>Sđt</th>
                            <th>Mã giao dịch</th>
                            <th>Giá vé</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="(v, k) in  listVe">
                            <tr class="text-center text-nowrap">
                                <td>@{{ v.thoi_gian_bat_dau }} <br> @{{ v.ngay_khoi_chay }} </td>
                                <td>@{{ v.ten_tuyen }}</td>
                                <td>@{{ v.ho_va_ten }}</td>
                                <td>@{{ v.bien_so_xe }}</td>
                                <td>@{{ v.ten_ghe }}</td>
                                <td>@{{ v.ten }}</td>
                                {{-- <td>@{{ v.id_khach_hang }}</td> --}}
                                <td>@{{ v.so_dien_thoai }}</td>
                                <td>@{{ v.ma_giao_dich }}</td>
                                <td>@{{ v.gia_ve }}</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        new Vue({
            el: '#app',
            data: {
                listVe: [],
            },
            created() {
                this.loadData();
            },
            methods: {
                loadData() {
                    axios
                        .get('/admin/quan-ly-ve/data')
                        .then((res) => {
                            this.listVe = res.data.data;
                        });
                },
            },
        });
    </script>
@endsection

