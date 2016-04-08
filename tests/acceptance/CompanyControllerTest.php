<?php

namespace Tests\Acceptance;

use Tests\AcceptanceTestCase;
use App\Entities\Company;

class CompanyControllerTest extends AcceptanceTestCase
{
    public function testView()
    {
        $this->visit('/')->see('mdl-navigation__link" href="'.$this->baseUrl.'/company">');
    
        $this->visit('/company')
            ->see('<i class="material-icons">filter_list</i>')
        ;
    }
    
    public function testCreate()
    {
        $this->visit('/company')->see('<a href="'.$this->baseUrl.'/company/create');
        
        $this->visit('/company/create');
    
        $this->type('Nome Empresa', 'name')
            ->type('measure units', 'measure_units')
            ->type('api token', 'api_token')
            ->press('Enviar')
            ->seePageIs('/company')
        ;
    
        $this->seeInDatabase(
            'companies',
            ['name' => 'Nome Empresa',
            'measure_units' => 'measure units',
            'api_token' => 'api token']
        );
    }
    
    public function testUpdate()
    {
        $this->visit('/company/'.Company::all()->last()['id'].'/edit');
        
        $this->type('Nome Empresa Editado', 'name')
            ->type('measure units editado', 'measure_units')
            ->type('api token editado', 'api_token')
            ->press('Enviar')
            ->seePageIs('/company')
        ;
        
        $this->seeInDatabase(
            'companies',
            ['name' => 'Nome Empresa Editado',
            'measure_units' => 'measure units editado',
            'api_token' => 'api token editado']
        );
    
    }
    
    public function testDelete()
    {
        $idDelete = Company::all()->last()['id'];
        
        $company = Company::find($idDelete);
        $company->contacts()->delete();
        $company->entries()->delete();
        $company->models()->delete();
        $company->trips()->delete();
        $company->types()->delete();
        $company->usersCompany()->delete();
        $company->usersPendingCompany()->delete();
        $company->vehicles()->delete();
        
        $this->seeInDatabase('companies', ['id' => $idDelete]);
        $this->visit('/company/destroy/'.$idDelete);
        $this->seeIsSoftDeletedInDatabase('companies', ['id' => $idDelete]);
    }
    
    public function testErrors()
    {
        $this->visit('/company/create')
            ->press('Enviar')
            ->seePageIs('/company/create')
            ->see('de um valor para o campo nome.</span>')
            ->see('de um valor para o campo api token.</span>')
        ;
    }
    
    public function testFilters()
    {
        $this->visit('/company')
            ->type('Company', 'name')
            ->type('City', 'city')
            ->type('Country', 'country')
            ->press('Buscar')
            ->see('Company</div>')
            ->see('City</div>')
            ->see('Country</div>')
        ;
    }
}
