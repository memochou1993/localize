<?php

use App\Models\Form;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    use HasStaticAttributes;

    public const AMOUNT = 2;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $forms = factory(Form::class, self::AMOUNT)->create();

        $this->set('forms', $forms);
    }
}
