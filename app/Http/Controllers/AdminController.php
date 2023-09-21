<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Jobs\Forgot_passwordJob;
use App\Models\Admin;
use App\Models\GheBan;
use App\Models\History;
use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // public function thong_ke(){

    //     return view('admin.page.thong_ke.index');

    //     }
    // public function test(){

    // return view('admin.page.test.html');

    // }

    public function viewLogin()
    {
        // Auth::guard('admin')->logout();
        return view('admin.page.login.login_admin');
    }

    public function logOut()
    {
        Auth::guard('admin')->logout();
        return redirect('/admin/login');
    }

    public function actionLogin(Request $request)
    {
        // dd($request->all());
        // Kiểm tra $request->email và $request->password có giống với tài khoản nào không?
        $data['email']      = $request->email;
        $data['password']   = $request->password;

        $check = Auth::guard('admin')->attempt($data); // True/False

        if ($check) {
            return response()->json([
                'status'    => true,
                'message'   => 'Đăng nhập thành công!'
            ]);
        } else {
            // Toastr::error('Tài khoản hoặc mật khẩu không chính xác');
            return response()->json([
                'status'    => false,
                'message'   => 'Tài khoản hoặc mật khẩu không chính xác!'
            ]);
        }
    }


    public function index()
    {
        $check = $this->checkRule_get(1);
        if(!$check) {
            toastr()->error('Bạn không có quyền truy cập chức năng này!');
            return redirect('/admin/lich-trinh/index');
        }
        return view('admin.page.tai_khoan.index');
    }
    public function create(Request $request){
        $check = $this->checkRule_get(1);
        if(!$check) {
            toastr()->error('Bạn không có quyền truy cập chức năng này!');
            return redirect('/admin/lich-trinh/index');
        }
        $data= $request->all();
        $data['password']=bcrypt($request->password);
        Admin:: create($data);
         return response()->json([
            'status' =>true

         ]);


    }
    public function data()
    {
        $check = $this->checkRule_get(1);
        if(!$check) {
            toastr()->error('Bạn không có quyền truy cập chức năng này!');
            return redirect('/admin/lich-trinh/index');
        }
        $data = Admin::get();

        return response()->json([
            'data'  => $data,
        ]);
    }
    public function history(){
        $check = $this->checkRule_get(1);
        if(!$check) {
            toastr()->error('Bạn không có quyền truy cập chức năng này!');
            return redirect('/admin/lich-trinh/index');
        }
        return view('admin.page.history_driver.time_sleep');
    }
    public function data_driver(){
        $check = $this->checkRule_get(1);
        if(!$check) {
            toastr()->error('Bạn không có quyền truy cập chức năng này!');
            return redirect('/admin/lich-trinh/index');
        }
        // $data = History::join('test', 'time_sleep.driver_id', 'test.id')
        //                ->select('time_sleep.*', 'test.name')
        //                ->get();
        $sql = "SELECT admins.ho_va_ten, time_sleep.* FROM `admins` JOIN time_sleep ON admins.id = time_sleep.drive_id WHERE admins.id_quyen = 2;";
        $data = DB::select($sql);

        return response()->json([
            'data'  => $data,
        ]);


    }

    public function destroy(Request $request)
    {

        $check = $this->checkRule_get(1);
        if(!$check) {
            toastr()->error('Bạn không có quyền truy cập chức năng này!');
            return redirect('/admin/lich-trinh/index');
        }
        $admin =  Admin::find($request->id);

        $admin->delete();
        return response()->json([
            'status'    => true,
        ]);
    }
    public function update(Request $request)
    {
        $check = $this->checkRule_get(1);
        if(!$check) {
            toastr()->error('Bạn không có quyền truy cập chức năng này!');
            return redirect('/admin/lich-trinh/index');
        }
        $data = $request->all();
        $admin = Admin::find($request->id);
        $admin->update($data);
        return response()->json([
            'status'    => true,
            'message' => 'Cập nhập tài khoản thành công!',
        ]);
    }

    public function viewEmail()
    {
        return view('admin.page.tai_khoan.view_nhap_email');
    }

    public function checkEmail(Request $request)
    {
        $admin = Admin::where('email', $request->email)->first();

        if ($admin) {
            $ma_reset  = Str::uuid();
            $admin->hash_reset = $ma_reset;
            $admin->save();

           // Gửi cho nó cái email
            $dataMail['full_name']  = $admin->ho_va_ten;
            $dataMail['mail_to']    = $admin->email;
            $dataMail['link']       = env('APP_URL') . '/reset-password/' . $ma_reset;
            $dataMail['tieu_de']    = 'Khôi phục mật khẩu';

            Forgot_passwordJob::dispatch($dataMail);

            return response()->json([
                'status'    => true,
                'message'   => 'Vui lòng kiểm tra email!'
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'Emai bạn không tồn tại!'
            ]);
        }
    }

    public function viewReset($hash)
    {
        // Bước 1: tìm xem hash đó có không?
        $khachHang = Admin::where('hash_reset', $hash)->first();
        if($khachHang) {
            Auth::guard('admin')->logout();
            return view('admin.page.tai_khoan.reset_pass', compact('hash'));
        } else {
            toastr()->error('Đường dẫn không tồn tại');
            return redirect('/admin/login');
        }
    }

    public function actionReset(ResetPasswordRequest $request)
    {
        $admin = Admin::where('hash_reset', $request->hash_reset)->first();
        $admin->hash_reset = null;
        $admin->password   = bcrypt($request->password);
        $admin->save();

        toastr()->success("Đã đổi mật khẩu thành công!");
        return redirect('/admin/login');
    }
    public function status(Request $request)
    {
        $check = $this->checkRule_get(1);
        if(!$check) {
            toastr()->error('Bạn không có quyền truy cập chức năng này!');
            return redirect('/admin/lich-trinh/index');
        }
        $quyen = Admin::find($request->id);
        if($quyen) {
            if($request->id_quyen == 1) {
                $quyen->id_quyen = 2;
            } else if($request->id_quyen == 2) {
                $quyen->id_quyen = 1;
            }
            $quyen->save();

            return response()->json([
                'status'    => 1,
                'message'   => 'Đã đổi quyền thành công!',
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Tuyến này không tồn tại!',
            ]);
        }
    }
    public function view_dskhachhang(){
        $check = $this->checkRule_get(1);
        if(!$check) {
            toastr()->error('Bạn không có quyền truy cập chức năng này!');
            return redirect('/admin/lich-trinh/index');
        }
        return view('admin.page.khach_hang.index');
    }

    public function dskhachang(){
        $check = $this->checkRule_get(1);
        if(!$check) {
            toastr()->error('Bạn không có quyền truy cập chức năng này!');
            return redirect('/admin/lich-trinh/index');
        }
        $dskhachhang = KhachHang::get();
        return response()->json([
            'data' => $dskhachhang,
        ]);
    }
    public function view_dsve(){
        $check = $this->checkRule_get(1);
        if(!$check) {
            toastr()->error('Bạn không có quyền truy cập chức năng này!');
            return redirect('/admin/lich-trinh/index');
        }
        return view('admin.page.quanlyve.index');
        }
        public function dsvedadat(){
            $check = $this->checkRule_get(1);
            if(!$check) {
                toastr()->error('Bạn không có quyền truy cập chức năng này!');
                return redirect('/admin/lich-trinh/index');
            }
                $data = GheBan::get();
                $data =GheBan::where('id_khach_hang', $data->id)->join('khach_hangs', 'khach_hangs.id', 'ghe_bans.id_khach_hang')
                ->join('lich_trinhs', 'lich_trinhs.id', 'ghe_bans.id_lich')
                ->join('tuyens', 'tuyens.id', 'lich_trinhs.id_tuyen')
                ->join('admins', 'admins.id', 'lich_trinhs.id_tai_xe')
                ->join('xe', 'xe.id', 'lich_trinhs.id_xe')->get();
                dd($data);
                return response()->json([
                    'data' =>  $data,
                ]);

            //     return response()->json([
            //     'data' =>  null,
            // ]);
            // $data = GheBan::get();
            // return response()->json([
            // 'data' => $data,
        // ]);

        }

}
