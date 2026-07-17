<?php

namespace Tests\Feature;

use App\Jobs\GenerateReportJob;
use App\Models\Company;
use App\Models\ReportDownload;
use App\Models\Resolution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_request_report_download()
    {
        Queue::fake();

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'type' => '1',
        ]);

        $company = Company::create([
            'name' => 'Test Company',
            'is_active' => 1
        ]);

        $resolution = Resolution::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'evsn_type' => '1',
            'start_date' => now()->subDays(2)->format('Y-m-d H:i:s'),
            'end_date' => now()->subDays(1)->format('Y-m-d H:i:s'),
            'is_active' => 1
        ]);

        $response = $this->actingAs($user)->postJson(route('votingreport.request_download'), [
            'resolution_id' => $resolution->id,
            'format' => 'excel'
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['success', 'message', 'download']);
        $this->assertDatabaseHas('report_downloads', [
            'user_id' => $user->id,
            'resolution_id' => $resolution->id,
            'report_type' => 'excel',
            'status' => 'queued'
        ]);

        Queue::assertDispatched(GenerateReportJob::class);
    }

    public function test_user_cannot_request_download_for_unauthorized_resolution()
    {
        Queue::fake();

        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
            'type' => '1',
        ]);

        $user2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
            'type' => '1',
        ]);

        $company = Company::create([
            'name' => 'Test Company',
            'is_active' => 1
        ]);

        // Owned by user2
        $resolution = Resolution::create([
            'user_id' => $user2->id,
            'company_id' => $company->id,
            'evsn_type' => '1',
            'start_date' => now()->subDays(2)->format('Y-m-d H:i:s'),
            'end_date' => now()->subDays(1)->format('Y-m-d H:i:s'),
            'is_active' => 1
        ]);

        // Request by user1
        $response = $this->actingAs($user1)->postJson(route('votingreport.request_download'), [
            'resolution_id' => $resolution->id,
            'format' => 'excel'
        ]);

        $response->assertStatus(403);
        Queue::assertNotDispatched(GenerateReportJob::class);
    }

    public function test_user_can_retrieve_download_list()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'type' => '1',
        ]);

        $company = Company::create([
            'name' => 'Test Company',
            'is_active' => 1
        ]);

        $resolution = Resolution::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'evsn_type' => '1',
            'start_date' => now()->subDays(2)->format('Y-m-d H:i:s'),
            'end_date' => now()->subDays(1)->format('Y-m-d H:i:s'),
            'is_active' => 1
        ]);

        ReportDownload::create([
            'user_id' => $user->id,
            'resolution_id' => $resolution->id,
            'report_type' => 'excel',
            'report_name' => 'company_report.xlsx',
            'status' => 'completed',
            'progress' => 100,
            'file_path' => 'downloads/company_report.xlsx'
        ]);

        $response = $this->actingAs($user)->getJson(route('votingreport.get_downloads'));

        $response->assertOk();
        $response->assertJsonCount(1, 'downloads');
        $response->assertJsonPath('downloads.0.report_name', 'company_report.xlsx');
    }

    public function test_user_can_securely_download_completed_file()
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'type' => '1',
        ]);

        $company = Company::create([
            'name' => 'Test Company',
            'is_active' => 1
        ]);

        $resolution = Resolution::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'evsn_type' => '1',
            'start_date' => now()->subDays(2)->format('Y-m-d H:i:s'),
            'end_date' => now()->subDays(1)->format('Y-m-d H:i:s'),
            'is_active' => 1
        ]);

        Storage::disk('public')->put('downloads/1/test_report.xlsx', 'test content');

        $download = ReportDownload::create([
            'user_id' => $user->id,
            'resolution_id' => $resolution->id,
            'report_type' => 'excel',
            'report_name' => 'test_report.xlsx',
            'status' => 'completed',
            'progress' => 100,
            'file_path' => 'downloads/1/test_report.xlsx'
        ]);

        $response = $this->actingAs($user)->get(route('votingreport.download_file', ['id' => $download->id]));

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'attachment; filename=test_report.xlsx');
        
        $this->assertDatabaseMissing('report_downloads', [
            'id' => $download->id
        ]);
    }
}
