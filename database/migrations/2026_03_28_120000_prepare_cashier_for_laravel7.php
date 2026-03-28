<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrepareCashierForLaravel7 extends Migration
{
    public function up()
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'pm_type')) {
                    $table->string('pm_type')->nullable()->after('stripe_id');
                }

                if (! Schema::hasColumn('users', 'pm_last_four')) {
                    $table->string('pm_last_four', 4)->nullable()->after('pm_type');
                }
            });
        }

        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                if (Schema::hasColumn('subscriptions', 'stripe_plan') && ! Schema::hasColumn('subscriptions', 'stripe_price')) {
                    $table->renameColumn('stripe_plan', 'stripe_price');
                }
            });

            Schema::table('subscriptions', function (Blueprint $table) {
                if (! Schema::hasColumn('subscriptions', 'stripe_status')) {
                    $table->string('stripe_status')->nullable()->after('stripe_price');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                if (Schema::hasColumn('subscriptions', 'stripe_status')) {
                    $table->dropColumn('stripe_status');
                }
            });

            Schema::table('subscriptions', function (Blueprint $table) {
                if (Schema::hasColumn('subscriptions', 'stripe_price') && ! Schema::hasColumn('subscriptions', 'stripe_plan')) {
                    $table->renameColumn('stripe_price', 'stripe_plan');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'pm_last_four')) {
                    $table->dropColumn('pm_last_four');
                }

                if (Schema::hasColumn('users', 'pm_type')) {
                    $table->dropColumn('pm_type');
                }
            });
        }
    }
}
