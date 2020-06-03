<?php

use App\Models\Role;
use App\Models\User;
use App\Traits\HasStaticAttributes;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use HasStaticAttributes;

    public const DATA_AMOUNT = 5;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = collect(config('permission.roles'))
            ->map(function ($item, $index) {
                $user = User::create([
                    'name' => $item['name'],
                    'email' => $index.'@email.com',
                    'password' => 'password',
                ]);

                $user->roles()->attach(
                    Role::where('name', $item['name'])->first()
                );

                return $user;
            })
            ->merge(
                factory(User::class, self::DATA_AMOUNT)->disableEvents()->create()
            );

        $this->set('users', $users);
    }
}
