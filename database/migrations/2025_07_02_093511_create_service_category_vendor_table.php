<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceCategoryVendorTable extends Migration
{
    public function up()
    {
        Schema::create('service_category_vendor', function (Blueprint $table) {
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_category_id')->constrained()->onDelete('cascade');
            $table->primary(['vendor_id', 'service_category_id']); // composite key
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_category_vendor');
    }
}
