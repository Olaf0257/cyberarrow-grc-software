<?php

namespace App\Console\Commands;

use App\Models\Compliance\Project;
use App\Models\DataScope\Scopable;
use App\Models\PolicyManagement\Campaign\Campaign;
use App\Models\PolicyManagement\Group\Group;
use App\Models\PolicyManagement\Policy;
use App\Models\RiskManagement\RiskRegister;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ScopeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-scope:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is responsible of setting the scope for previously existing data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->newLine(2);

        $this->projectsScope();

        $this->groupScope();

        $this->campaignScope();

        $this->policyScope();

        $this->riskRegisterScope();

        $this->newLine();
        $this->info('Done.');
        return 0;
    }

    /**
     * @return void
     * Compliance module
     * Add default data scope to existing client project and project controls data
     */
    private function projectsScope()
    {
        $this->newLine();
        $this->info('Scoping projects and project controls');
        $this->newLine();

        $projects = Project::with('controls')->get();

        $this->addScope($projects, "controls" );

        $this->newLine();
        $this->info('Finished scoping projects and project controls.');
        $this->newLine(2);
    }

    /**
     * @return void
     * Policy module
     */
    private function groupScope(){
        $this->newLine();
        $this->info('Scoping groups and group users');
        $this->newLine();

        $groups = Group::with('users')->get();

        $this->addScope($groups, "users");

        $this->newLine();
        $this->info('Finished scoping groups and group users.');
        $this->newLine(2);
    }

    /**
     * @return void
     * Policy module
     */
    private function campaignScope(){
        $this->newLine();
        $this->info('Scoping campaigns');
        $this->newLine();

        $campaigns = Campaign::get();

        $this->addScope($campaigns);

        $this->newLine();
        $this->info('Finished scoping campaigns.');
        $this->newLine(2);
    }

    /**
     * @return void
     * Policy module
     */
    private function policyScope(){
        $this->newLine();
        $this->info('Scoping policies');
        $this->newLine();

        $campaigns = Policy::get();

        $this->addScope($campaigns);

        // policy users scope
        $policy_users=\App\Models\PolicyManagement\User::get();
        $this->addScope($policy_users);

        $this->newLine();
        $this->info('Finished scoping policies.');
        $this->newLine(2);
    }

    /**
     * @return void
     * Risk module
     */
    private function riskRegisterScope(){
        $this->newLine();
        $this->info('Scoping registered risks');
        $this->newLine();

        $registeredRisks = RiskRegister::get();
        $this->addScope($registeredRisks);

        $this->newLine();
        $this->info('Finished scoping registered risks.');
        $this->newLine(2);
    }

    /**
     * @param $items
     * @param $relationship
     * @return void
     * Dynamically add scope for all needed models
     */
    private function addScope($items ,$relationship = null){
        $errors = 0;
        $scoped_items = 0;
        $unscoped_items_count = 0;

        $bar = $this->output->createProgressBar($items->count());
        if($items->count() > 0 ){
            $bar->start();
        }

        DB::beginTransaction();
        foreach($items as $item){
            $bar->advance();
            $unscoped_items = Scopable::
            where('scopable_id', $item->id)
                ->where('scopable_type', get_class($item))
                ->doesntExist();
            if ($unscoped_items) {
                $unscoped_items_count++;

                $scoped_item = Scopable::create([
                    'organization_id' => 1,
                    'scopable_id' => $item->id,
                    'scopable_type' => get_class($item)
                ]);
                if ($scoped_item) $scoped_items++;
            }

            if($relationship){
                $rel_items = $item->$relationship;
                if( count($rel_items) > 0 ){
                    $model = get_class($rel_items->first());

                    $all_scoped_rel_items = Scopable::where('scopable_type', $model)->get();

                    $all_rel_item_ids = $rel_items->pluck('id')->toArray();
                    $all_scoped_rel_item_ids = $all_scoped_rel_items->pluck('scopable_id')->toArray();

                    $unscoped_rel_item_ids = array_diff($all_rel_item_ids, $all_scoped_rel_item_ids);
                    $scoped_rel_items = 0;

                    if(count($unscoped_rel_item_ids) > 0) {
                        $this->newLine();
                        $this->info(sprintf('Scoping %d %s', count($unscoped_rel_item_ids), $relationship));

                        foreach ($unscoped_rel_item_ids as $rel_item_id) {
                            $scoped_rel_item = Scopable::create([
                                'organization_id' => 1,
                                'scopable_id' => $rel_item_id,
                                'scopable_type' => $model
                            ]);
                            if ($scoped_rel_item) $scoped_rel_items++;
                        }
                    }
                    if($scoped_rel_items !== count($unscoped_rel_item_ids)) {
                        $errors++;
                    }
                }
            }
        }

        if ($items->count() > 0) {
            $bar->finish();
        }

        if ($scoped_items !== $unscoped_items_count) {
            $errors++;
        }

        $this->newLine();
        if ($errors > 0) {
            $this->warn('Something went wrong, rolling back ...');
            DB::rollBack();
        } else {
            $this->warn(sprintf('Scoped items: %d', $scoped_items));
            DB::commit();
        }

    }
}
