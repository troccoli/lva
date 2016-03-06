<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 10:58
 */

namespace Admin\DataManagement;


class HomePageTest extends \TestCase
{
    private $admin;
    
    protected function setUp()
    {
        parent::setUp();
        
        $this->admin = $this->getFakeUser();
    }

    public function testRedirectToLoginIfNotAdmin()
    {
        $this->visit(route('admin::dataManagement'))
            ->seePageIs(route('login'));
    }
    
    public function testSeasonsTableButton()
    {
        $this->be($this->admin);
        
        $this->visit(route('admin::dataManagement'))
            ->seeLink('Seasons', route('admin::dataManagement::seasons'));
    }
}
