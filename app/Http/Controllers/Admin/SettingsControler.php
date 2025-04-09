<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsControler extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.settings.index', ['allSettings' => getAllSettings()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $key)
    {
        // Check if this is a file upload type setting
        $fileTypes = ['logo', 'fav_icon', 'banner'];
        
        if (in_array($key, $fileTypes) && $request->hasFile('settings_file')) {
            // Handle file upload
            $file = $request->file('settings_file');
            
            // Delete old file if it exists
            $oldFile = $request->settings_value;
            if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                Storage::disk('public')->delete($oldFile);
            }
            
            // Store the new file
            $path = $file->store('settings', 'public');
            
            // Update setting with new file path
            $response = updateSettings($key, $path);
        } else {
            // Handle regular text settings
            $response = updateSettings($key, $request->settings_value);
        }

        if ($response) {
            toast()->success('Best wishes', 'Settings updated successfully.');
            return back();
        }

        toast()->error('Failed', 'Settings Updated Failed.');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $key)
    {
        // Check if this is a file setting before deleting
        $fileTypes = ['logo', 'fav_icon', 'banner'];
        
        if (in_array($key, $fileTypes)) {
            $filePath = getSettings($key);
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        }
        
        if (deleteSettings($key)) {
            toast()->success('Best wishes', 'Settings deleted successfully.');
            return back();
        }

        toast()->error('Failed', 'Settings Deleted Failed.');
        return back();
    }
}