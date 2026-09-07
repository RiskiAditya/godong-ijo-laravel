<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\SchoolPartner;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test for School Partners full list page
 * 
 * Task: 19.2 Create schoolPartners() method in StaticPageController
 * Requirements:
 * - Query all active school partners ordered by display order
 * - Paginate results (20 per page)
 * - Pass data to view with navigation and SEO metadata
 */
class SchoolPartnersTest extends TestCase
{
    /**
     * Test that the school partners route exists and returns 200 status
     *
     * @return void
     */
    public function test_school_partners_route_exists()
    {
        $response = $this->get('/wisata-edukasi/sekolah-mitra');
        
        $response->assertStatus(200);
    }
    
    /**
     * Test that the school partners page displays correctly
     *
     * @return void
     */
    public function test_school_partners_page_displays_data()
    {
        $response = $this->get(route('education.school-partners'));
        
        $response->assertStatus(200);
        $response->assertViewIs('pages.school-partners');
        $response->assertViewHas('schoolPartners');
        $response->assertViewHas('breadcrumbs');
        $response->assertViewHas('seoData');
        $response->assertViewHas('navigation');
    }
    
    /**
     * Test that pagination works correctly (20 per page)
     *
     * @return void
     */
    public function test_school_partners_pagination()
    {
        $response = $this->get(route('education.school-partners'));
        
        $response->assertStatus(200);
        
        // Get the paginated data from the view
        $schoolPartners = $response->viewData('schoolPartners');
        
        // Verify pagination settings
        $this->assertLessThanOrEqual(20, $schoolPartners->count());
        $this->assertEquals(20, $schoolPartners->perPage());
    }
    
    /**
     * Test that only active school partners are displayed
     *
     * @return void
     */
    public function test_only_active_school_partners_displayed()
    {
        $response = $this->get(route('education.school-partners'));
        
        $response->assertStatus(200);
        
        // Get the paginated data from the view
        $schoolPartners = $response->viewData('schoolPartners');
        
        // Verify all displayed partners are active
        foreach ($schoolPartners as $partner) {
            $this->assertTrue($partner->is_active);
        }
    }
    
    /**
     * Test that school partners are ordered correctly
     *
     * @return void
     */
    public function test_school_partners_ordered_by_display_order()
    {
        $response = $this->get(route('education.school-partners'));
        
        $response->assertStatus(200);
        
        // Get the paginated data from the view
        $schoolPartners = $response->viewData('schoolPartners');
        
        // Verify ordering (each subsequent item should have order >= previous)
        $previousOrder = -1;
        foreach ($schoolPartners as $partner) {
            $this->assertGreaterThanOrEqual($previousOrder, $partner->order);
            $previousOrder = $partner->order;
        }
    }
    
    /**
     * Test that breadcrumbs include proper hierarchy
     *
     * @return void
     */
    public function test_breadcrumbs_structure()
    {
        $response = $this->get(route('education.school-partners'));
        
        $response->assertStatus(200);
        
        // Verify breadcrumbs exist
        $breadcrumbs = $response->viewData('breadcrumbs');
        $this->assertNotEmpty($breadcrumbs);
    }
    
    /**
     * Test that SEO metadata is present
     *
     * @return void
     */
    public function test_seo_metadata_present()
    {
        $response = $this->get(route('education.school-partners'));
        
        $response->assertStatus(200);
        
        // Verify SEO data exists
        $seoData = $response->viewData('seoData');
        $this->assertNotEmpty($seoData);
        $this->assertArrayHasKey('title', $seoData);
        $this->assertArrayHasKey('description', $seoData);
    }
}
