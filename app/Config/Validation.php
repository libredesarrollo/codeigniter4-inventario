<?php

namespace Config;

use App\Validation\UserRules;
use CodeIgniter\Validation\CreditCardRules;
use CodeIgniter\Validation\FileRules;
use CodeIgniter\Validation\FormatRules;
use CodeIgniter\Validation\Rules;

class Validation
{
    //--------------------------------------------------------------------
    // Setup
    //--------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
        UserRules::class
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    public $categories =[
		'name' => 'required|min_length[3]|max_length[255]'
	];
    public $tags =[
		'name' => 'required|min_length[3]|max_length[255]'
	];
    public $products =[
		'name' => 'required|min_length[3]|max_length[255]',
		'description' => 'required|min_length[3]|max_length[2000]',
		'code' => 'required|min_length[3]|max_length[10]',
		'entry' => 'required|is_natural',
		'exit' => 'required|is_natural',
		'stock' => 'required|is_natural',
		'category_id' => 'required|is_natural',
		'tag_id' => 'permit_empty|is_natural',
		'price' => 'required|decimal',
	];


    //--------------------------------------------------------------------
    // Rules
    //--------------------------------------------------------------------
}
