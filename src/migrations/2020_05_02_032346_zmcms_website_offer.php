<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ZmcmsWebsiteOffer extends Migration{
	public function up(){
		$tblNamePrefix=(Config('database.prefix')??'');
		$tblName=$tblNamePrefix.'offers';
		Schema::create($tblName, function($table){$table->string('token', 70);});
		Schema::table($tblName, function($table){$table->integer('sort', false, true)->nullable();});	//	Sortowanie kolejności wyświetlania rekordów
		Schema::table($tblName, function($table){$table->string('access', 70)->default('*');}); // Info, które grupy użytkowników mają dostęp do danej pozycji nawigacji. "*" -> wszyscy mają dostęp, "{'a', 'b', 'd'}" ->grupy a, b oraz d mają dostęp do artykułu
		Schema::table($tblName, function($table){$table->string('frontend_access', 70)->default('*');}); // Info, które grupy użytkowników "z frontu" mają dostęp do danej pozycji nawigacji. "*" -> wszyscy mają dostęp, "{'a', 'b', 'd'}" ->grupy a, b oraz d mają dostęp do artykułu
		Schema::table($tblName, function($table){$table->string('active', 1);}); //Aktywny - 1, Nieaktywny -0. Aktywny się wyświetla, nieaktywny nie.
		Schema::table($tblName, function($table){$table->string('type', 10);}); // Rodzaj oferty, zgodny z plikiem konfiguracyjnym website_offer.php
		Schema::table($tblName, function($table){$table->string('ilustration', 150)->nullable();});// Ilustracja kategorii
		Schema::table($tblName, function($table){$table->text('images_resized')->nullable();});// Ilustracja kategorii
		Schema::table($tblName, function($table){$table->decimal('price_brut', 12, 2);});			//	Katalogowa cena brutto
		Schema::table($tblName, function($table){$table->decimal('price_brut_min', 12, 2)->nullable();});		//	Minimalna cena, poniżej której nie można kupić produktu /usługi
		Schema::table($tblName, function($table){$table->decimal('promo', 2, 2)->nullable();}); 	// Promocja na cenie, wyrażona w %
		Schema::table($tblName, function($table){$table->decimal('points', 12, 2)->nullable();});	//	Liczba punktów przyznawanych za skorzystanie z oferty
		Schema::table($tblName, function($table){$table->string('date_from', 10);}); // data od kiedy wyświetla się dana oferta,
		Schema::table($tblName, function($table){$table->string('date_to', 10)->nullable();}); // data do kiedy wyświetla się dana oferta, (null - wyświetla się zawsze)
		Schema::table($tblName, function($table){$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));});//Imię
		Schema::table($tblName, function($table){$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));});
		Schema::table($tblName, function($table){$table->primary(['token'], 'zmcmokey1');}); // Link w ramach języka musi być niepowtarzalny

		
		$tblName=$tblNamePrefix.'offers_names';
		Schema::create($tblName, function($table){$table->string('token', 70);});
		Schema::table($tblName, function($table){$table->string('langs_id', 5);}); // 
		Schema::table($tblName, function($table){$table->string('name', 5);}); // 
		Schema::table($tblName, function($table){$table->string('slug', 5)->unique();}); // 
		Schema::table($tblName, function($table){$table->text('intro');}); // 
		Schema::table($tblName, function($table){$table->primary(['token', 'langs_id'], 'zmcmonkey1');});
		Schema::table($tblName, function($table){$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));});//Imię
		Schema::table($tblName, function($table){$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));});
		Schema::table($tblName, function($table) use ($tblNamePrefix){$table->foreign('token')->references('token')->on($tblNamePrefix.'offers')->onUpdate('cascade')->onDelete('cascade');});



		$tblName=$tblNamePrefix.'product';
		Schema::create($tblName, function($table){$table->string('token', 70);});
		Schema::table($tblName, function($table){$table->string('code', 50)->nullable()->unique();}); 			//Kod produktu
		Schema::table($tblName, function($table){$table->string('producer_code', 50)->nullable()->unique()->after('code');});
		Schema::table($tblName, function($table){$table->string('producer', 50)->nullable()->unique()->after('producer_code');});
		Schema::table($tblName, function($table){$table->string('ean13', 13)->nullable()->unique();}); 		// Kod EAN 13
		Schema::table($tblName, function($table){$table->string('ean128', 40)->nullable();;}); 		//Kod kreskowy, np. magazynowy
		Schema::table($tblName, function($table){$table->string('link', 120)->nullable();;}); 		// Link do informacji o produkcie - np link do producenta
		Schema::table($tblName, function($table){$table->string('ilustration', 150)->nullable();});// Ilustracja kategorii
		Schema::table($tblName, function($table){$table->text('images_resized')->nullable();});// Ilustracja kategorii
		Schema::table($tblName, function($table){$table->string('on_sale', 1);});//1- w sprzedaży, 0 - nie w sprzedaży
		Schema::table($tblName, function($table){$table->string('supply_type', 50)->nullable();});//Produkcja, dostawa z zewnątrz (potrzebne do generowania zleceń)
		Schema::table($tblName, function($table){$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));});//Imię
		Schema::table($tblName, function($table){$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));});
		Schema::table($tblName, function($table){$table->primary(['token'], 'zmcmpkey1');});


		$tblName=$tblNamePrefix.'product_names';
		Schema::create($tblName, function($table){$table->string('token', 70);});
		Schema::table($tblName, function($table){$table->string('langs_id', 5);}); // 
		Schema::table($tblName, function($table){$table->string('name', 50);}); // 
		Schema::table($tblName, function($table){$table->string('slug', 50);}); // 
		Schema::table($tblName, function($table){$table->string('in_composition_name', 50)->nullable();}); // Wyświetlana w widoku, gdy dana pozycja jest składnikiem większego produktu
		Schema::table($tblName, function($table){$table->text('intro');}); // 
		Schema::table($tblName, function($table){$table->string('meta_keywords', 150)->nullable();});
		Schema::table($tblName, function($table){$table->string('meta_description', 150)->nullable();});
		Schema::table($tblName, function($table){$table->string('og_title', 150)->nullable();});
		Schema::table($tblName, function($table){$table->string('og_type', 150)->nullable();});
		Schema::table($tblName, function($table){$table->string('og_url', 150)->nullable();});
		Schema::table($tblName, function($table){$table->string('og_image', 150)->nullable();});
		Schema::table($tblName, function($table){$table->string('og_description', 150)->nullable();});
		Schema::table($tblName, function($table){$table->primary(['token', 'langs_id'], 'zmcmspnkey1');});
		Schema::table($tblName, function($table){$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));});//Imię
		Schema::table($tblName, function($table){$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));});
		Schema::table($tblName, function($table) use ($tblNamePrefix){$table->foreign('token')->references('token')->on($tblNamePrefix.'product')->onUpdate('cascade')->onDelete('cascade');});

		$tblName=$tblNamePrefix.'product_description_content';
		Schema::create($tblName, function($table){$table->string('token', 70);});
		Schema::table($tblName, function($table){$table->string('langs_id', 5);});
		Schema::table($tblName, function($table){$table->text('description');}); 
		Schema::table($tblName, function($table){$table->primary(['token', 'langs_id'], 'zmcmspdckey1');});
		Schema::table($tblName, function($table) use ($tblNamePrefix){$table->foreign('token')->references('token')->on($tblNamePrefix.'product')->onUpdate('cascade')->onDelete('cascade');});

$tblName=$tblNamePrefix.'product_compositions_groups';
Schema::create($tblName, function($table){$table->string('group', 20);});
Schema::table($tblName, function($table){$table->integer('sort', false, true)->nullable();});
Schema::table($tblName, function($table){$table->string('choices', 10);});
Schema::table($tblName, function($table){$table->string('run', 150);});
Schema::table($tblName, function($table){$table->string('obligatory', 1)->after('run');}); //Czy składnik jest obowiązkowy 0 -NIE 1 - TAK
Schema::table($tblName, function($table){$table->primary(['group'], 'zmcmspcgkey1');});
$tblName=$tblNamePrefix.'product_compositions_groups_names';
Schema::create($tblName, function($table){$table->string('group', 20);});
Schema::table($tblName, function($table){$table->string('langs_id', 5);});
Schema::table($tblName, function($table){$table->string('name', 30);});
Schema::table($tblName, function($table){$table->primary(['group', 'langs_id'], 'zmcmspcgnkey1');});
Schema::table($tblName, function($table) use ($tblNamePrefix){$table->foreign('group')->references('group')->on($tblNamePrefix.'product_compositions_groups')->onUpdate('cascade')->onDelete('cascade');});

// $q = DB::table($tblNamePrefix.'product_compositions_groups');
// $q->insert(['group'=>'dough', 		'sort'=>'0', 'choices'=>'single', 	'run'=>'Controller@method0', ]);
// $q->insert(['group'=>'base', 		'sort'=>'1', 'choices'=>'single', 	'run'=>'Controller@method1', ]);
// $q->insert(['group'=>'topping', 	'sort'=>'2', 'choices'=>'single', 	'run'=>'Controller@method2', ]);
// $q->insert(['group'=>'additives', 	'sort'=>'3', 'choices'=>'multiple', 'run'=>'Controller@method3', ]);
// 
// $x = DB::table($tblNamePrefix.'product_compositions_groups_names');
// $x->insert(['group'=>'dough','langs_id'=>'pl',		'name'=>'Ciasto do pizzy',]);
// $x->insert(['group'=>'base','langs_id'=>'pl',		'name'=>'Podkład do pizzy',]);
// $x->insert(['group'=>'topping','langs_id'=>'pl',	'name'=>'Warstwa wierchnia',]);
// $x->insert(['group'=>'additives','langs_id'=>'pl',	'name'=>'Dodatki',]);

$tblName=$tblNamePrefix.'dict_product_parameters';
Schema::create($tblName, function($table){$table->string('parameter', 20);});
Schema::table($tblName, function($table){$table->integer('sort', false, true)->nullable();});
Schema::table($tblName, function($table){$table->string('obligatory', 1);});
Schema::table($tblName, function($table){$table->string('countable', 1);}); // Parametr może być policzalny lub opisowy. 1 - policzalny, 0 - opisowy
Schema::table($tblName, function($table){$table->primary(['parameter'], 'zmcmspdpkey1');});
$tblName=$tblNamePrefix.'dict_product_parameters_names';
Schema::create($tblName, function($table){$table->string('parameter', 20);});
Schema::table($tblName, function($table){$table->string('langs_id', 5);});
Schema::table($tblName, function($table){$table->string('name', 30);});
Schema::table($tblName, function($table){$table->text('units')->nullable();}); // Jeżeli parametr jest policzalny, tutaj podaje się zestaw jednostek w formacie json z przelicznikami: np. 1kg = 1000g. Json opisano pod tabelą.
Schema::table($tblName, function($table){$table->text('description')->nullable();});
Schema::table($tblName, function($table){$table->primary(['parameter', 'langs_id'], 'zmcmspdpkey1');});
Schema::table($tblName, function($table) use ($tblNamePrefix){$table->foreign('parameter')->references('parameter')->on($tblNamePrefix.'dict_product_parameters')->onUpdate('cascade')->onDelete('cascade');});

/**
 * JSON jednostek;
 	{
		{unit: m, name: metr, main: 1, converter: 1},
		{unit: mm, name: milimetr, main: 1, converter: 1/1000},
		{unit: cm, name: centymetr, main: 1, converter: 1/100},
		{unit: km, name: kilometr, main: 1, converter: 1000},
		{unit: dm, name: decymatr, main: 1, converter: 10},
 	}
 */


$tblName=$tblNamePrefix.'product_parameters';
Schema::create($tblName, function($table){$table->string('token', 70);});
Schema::create($tblName, function($table){$table->string('parameter', 20);});
Schema::table($tblName, function($table){$table->primary(['token', 'parameter'], 'zmcmsppkey1');});

$tblName=$tblNamePrefix.'product_parameters_desctiptions';

$tblName=$tblNamePrefix.'product_compositions';
Schema::create($tblName, function($table){$table->string('rid', 70)->nullable()->unique()->before('token');});//Unikalny identyfikator rekordu tabeli
Schema::table($tblName, function($table){$table->string('p', 70)->nullable();});//Rodzic (wskazuje na rid)
Schema::table($tblName, function($table){$table->string('token', 70)->nullable();});//Token produktu będącego składnikiem
Schema::table($tblName, function($table){$table->string('composition_token', 70)->nullable()->after('token');});//Token produktu będącego składnikiem
Schema::table($tblName, function($table){$table->integer('sort', false, true)->nullable();});	//	Sortowanie kolejności wyświetlania rekordów
Schema::table($tblName, function($table){$table->string('group', 20)->nullable();});	//	Grupa opcji (Np. wybór ciasta do pizzy, kolor itd itp)
Schema::table($tblName, function($table){$table->string('select', 20)->nullable();});	//	Checkbox lub radio
Schema::table($tblName, function($table){$table->string('price_affected', 1);}); //Czy wpływa na cenę? 0 - niw wpływa na cenę, 1 - wpływa (dodawanie i odejmowanie), 2 - wpływa częściowo (gdy tylko dodajemy)
Schema::table($tblName, function($table){$table->string('default', 1);}); //Czy ta część jest domyślnie wybrana? 0 - NIE, 1 - TAK
Schema::table($tblName, function($table){$table->decimal('default_q', 12, 2);}); //Domyślna ilość składnika
Schema::table($tblName, function($table){$table->decimal('max_q', 12, 2);}); //MAksymalna ilość składnika
Schema::table($tblName, function($table){$table->decimal('price_brut', 12, 2)->nullable();});		//	Cena do zapłaty danego składnika
Schema::table($tblName, function($table){$table->decimal('vat', 2, 2)->nullable();}); //VAT w setnych całości, np. 0.23
Schema::table($tblName, function($table){$table->primary(['rid'], 'zmcmspckey1');});
Schema::table($tblName, function($table){$table->unique(['rid', 'p']);});

$tblName=$tblNamePrefix.'product_compositions_names';
Schema::create($tblName, function($table){$table->string('rid', 70);});//Unikalny identyfikator rekordu tabeli
Schema::table($tblName, function($table){$table->string('langs_id', 5);});
Schema::table($tblName, function($table){$table->string('name', 70);});
Schema::table($tblName, function($table){$table->primary(['rid', 'langs_id'], 'zmcmspckey1n');});
Schema::table($tblName, function($table) use ($tblNamePrefix){$table->foreign('rid')->references('rid')->on($tblNamePrefix.'product_compositions')->onUpdate('cascade')->onDelete('cascade');});


		$tblName=$tblNamePrefix.'product_related_articles';
		Schema::create($tblName, function($table){$table->string('product_token', 70);});
		Schema::table($tblName, function($table){$table->string('article_token', 70);});
		Schema::table($tblName, function($table){$table->primary(['product_token', 'article_token'], 'zmcmspakey1');});
		Schema::table($tblName, function($table) use ($tblNamePrefix){$table->foreign('product_token')->references('token')->on($tblNamePrefix.'product')->onUpdate('cascade')->onDelete('cascade');});
		if(Schema::hasTable($tblNamePrefix.'website_articles')){Schema::table($tblName, function($table) use ($tblNamePrefix){$table->foreign('article_token')->references('token')->on($tblNamePrefix.'website_articles')->onUpdate('cascade')->onDelete('cascade');});};

		$tblName=$tblNamePrefix.'offers_relations';
		Schema::create($tblName, function($table){$table->string('offers_token', 70);});
		Schema::table($tblName, function($table){$table->string('object_token', 70);});
		Schema::table($tblName, function($table){$table->decimal('q', 12, 2)->after('object_token');}); //Ilość obiektów (np. jednostek produktu) w danej ofercie
		Schema::table($tblName, function($table){$table->string('parameters', 400);});  // ewentualne parametry w formacie json
		Schema::table($tblName, function($table){$table->integer('sort', false, true)->nullable();});	//	Sortowanie kolejności wyświetlania rekordów
		Schema::table($tblName, function($table){$table->primary(['offers_token', 'object_token'], 'zmcmsprkey1');});
		Schema::table($tblName, function($table) use ($tblNamePrefix){$table->foreign('offers_token')->references('token')->on($tblNamePrefix.'offers')->onUpdate('cascade')->onDelete('cascade');});
		// Schema::table($tblName, function($table) use ($tblNamePrefix){$table->foreign('object_token')->references('token')->on($tblNamePrefix.'product')->onUpdate('cascade')->onDelete('cascade');});
		// Schema::table($tblName, function($table) use ($tblNamePrefix){$table->foreign('object_token')->references('token')->on($tblNamePrefix.'website_articles')->onUpdate('cascade')->onDelete('cascade');});

		// GDY CHCESZ ZROBIĆ RELACJĘ JESZCZE DO OLEJNEJ TABELI, NAZWIJ KLUCZ OBCY
		// Schema::table($tblName, function($table) use ($tblNamePrefix){$table->foreign('object_token', 'orfk1')->references('token')->on($tblNamePrefix.'website_articles')->onUpdate('cascade')->onDelete('cascade');});

	}
	public function down(){
	}
}
