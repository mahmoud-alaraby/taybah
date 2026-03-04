<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $this->call(AdminsTableSeeder::class);
        $this->call(CacheTableSeeder::class);
        $this->call(CacheLocksTableSeeder::class);
        $this->call(CustomerCommunicationsTableSeeder::class);
        $this->call(CustomerMovementTargetsTableSeeder::class);
        $this->call(CustomerMovementsTableSeeder::class);
        $this->call(CustomerResponseCategoriesTableSeeder::class);
        $this->call(CustomerResponsesTableSeeder::class);
        $this->call(DailyTasksTableSeeder::class);
        $this->call(DailyWorkSummariesTableSeeder::class);
        $this->call(DesignerTaskAccountsTableSeeder::class);
        $this->call(EmployeeAttendancesTableSeeder::class);
        $this->call(EmployeeRolesTableSeeder::class);
        $this->call(EmployeesTableSeeder::class);
        $this->call(FailedJobsTableSeeder::class);
        $this->call(JobBatchesTableSeeder::class);
        $this->call(JobsTableSeeder::class);
        $this->call(MigrationsTableSeeder::class);
        $this->call(MonthlyTargetsTableSeeder::class);
        $this->call(OperationTasksTableSeeder::class);
        $this->call(PasswordResetTokensTableSeeder::class);
        $this->call(PaymentsTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(PhotographyBookingsTableSeeder::class);
        $this->call(PhotographyCostsTableSeeder::class);
        $this->call(PotentialCustomerClassificationHistoryTableSeeder::class);
        $this->call(PotentialCustomerClassificationsTableSeeder::class);
        $this->call(PotentialCustomersTableSeeder::class);
        $this->call(CustomerChatsTableSeeder::class);
        $this->call(CustomerChatMessagesTableSeeder::class);
        $this->call(PrintBookingsTableSeeder::class);
        $this->call(PrintMonthlyTargetsTableSeeder::class);
        $this->call(PrintPaymentsTableSeeder::class);
        $this->call(PrintReceiptsTableSeeder::class);
        $this->call(ProjectEmployeesTableSeeder::class);
        $this->call(ProjectTasksTableSeeder::class);
        $this->call(ProjectsTableSeeder::class);
        $this->call(ReceiptsTableSeeder::class);
        $this->call(RenewalDatesTableSeeder::class);
        $this->call(RolePermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(SessionsTableSeeder::class);
        $this->call(TimeTrackingTableSeeder::class);
        $this->call(TimeTrackingsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(WorkChatMessagesTableSeeder::class);
        $this->call(WorkChatsTableSeeder::class);
    }
}
