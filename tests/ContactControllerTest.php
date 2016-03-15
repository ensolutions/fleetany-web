<?php

use App\Entities\Contact;

class ContactControllerTest extends TestCase
{
    public function testView()
    {
        $this->visit('/')->see('contact">Contatos');
    
        $this->visit('/contact')
            ->see('de acesso para esta p', true)
        ;
    }
    
    public function testCreate()
    {
        $this->visit('/contact')->see('Novo');
        
        $this->visit('/contact/create');
    
        $this->type('Nome Contato', 'name')
            ->type('Brasil', 'country')
            ->type('RS', 'state')
            ->type('Porto Alegre', 'city')
            ->type('Adress', 'address')
            ->type('(99) 9999-9999', 'phone')
            ->type('License', 'license_no')
            ->press('Enviar')
            ->seePageIs('/contact')
        ;
    
        $this->seeInDatabase('contacts', 
                [
                    'name' => 'Nome Contato',
                    'country' => 'Brasil',
                    'state' => 'RS',
                    'city' => 'Porto Alegre',
                    'address' => 'Adress',
                    'phone' => '(99) 9999-9999',
                    'license_no' => 'License',
                ]);
    }
    
    public function testUpdate()
    {
        $this->visit('/contact/'.Contact::all()->last()['id'].'/edit');
        
        $this->type('Nome Contato Editado', 'name')
            ->type('Brasil2', 'country')
            ->type('RS2', 'state')
            ->type('Porto Alegre2', 'city')
            ->type('Adress2', 'address')
            ->type('(99) 9999-9998', 'phone')
            ->type('License2', 'license_no')
            ->press('Enviar')
            ->seePageIs('/contact')
        ;
    
        $this->seeInDatabase('contacts', 
                [
                    'name' => 'Nome Contato Editado',
                    'country' => 'Brasil2',
                    'state' => 'RS2',
                    'city' => 'Porto Alegre2',
                    'address' => 'Adress2',
                    'phone' => '(99) 9999-9998',
                    'license_no' => 'License2',
                ]);
    }
    
    public function testDelete()
    {
        $this->seeInDatabase('contacts', ['id' => 1]);
        $this->visit('/contact');
        $idOption = $this->crawler->filterXPath("//a[@name='Excluir']")->eq(0)->attr('name');
        $crawler = $this->click($idOption);
        $this->seeIsSoftDeletedInDatabase('contacts', ['id' => 1]);
    }
}