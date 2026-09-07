<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\SystemSetting;

class SettingController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        
        $settings = [
            'site_name' => 'Godong Ijo',
            'contact_email' => config('mail.from.address'),
            'contact_phone' => '+62 123 4567 890',
            'booking_notification' => SystemSetting::enabled('booking_notification'),
            'email_notification' => SystemSetting::enabled('email_notification'),
        ];
        
        return view('admin.settings.index', compact('admin', 'settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        // Update profile
        if ($request->has('name')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
            ]);
            
            $admin->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
            
            return redirect()->route('admin.settings')
                ->with('success', 'Profil berhasil diperbarui');
        }
        
        // Update password
        if ($request->has('current_password')) {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:6|confirmed',
            ]);
            
            if (!Hash::check($request->current_password, $admin->password)) {
                return redirect()->route('admin.settings')
                    ->with('error', 'Password lama tidak sesuai');
            }
            
            $admin->update([
                'password' => Hash::make($request->new_password),
            ]);
            
            return redirect()->route('admin.settings')
                ->with('success', 'Password berhasil diubah');
        }
        
        // Update system settings (placeholder)
        if ($request->has('site_name')) {
            $request->validate([
                'site_name' => 'required|string|max:255',
                'contact_email' => 'required|email|max:255',
                'contact_phone' => 'required|string|max:30',
            ]);

            SystemSetting::set('booking_notification', $request->boolean('booking_notification'));
            SystemSetting::set('email_notification', $request->boolean('email_notification'));

            return redirect()->route('admin.settings')
                ->with('success', 'Pengaturan sistem berhasil diperbarui');
        }
        
        return redirect()->route('admin.settings');
    }
}
