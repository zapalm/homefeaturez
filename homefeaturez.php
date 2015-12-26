<?php
/**
 * Featured products on the homepage: module for PrestaShop 1.3-1.4
 *
 * @author zapalm <zapalm@ya.ru>
 * @copyright (c) 2010-2015, zapalm
 * @link http://prestashop.modulez.ru/en/frontend-features/16-enhanced-featured-products-on-homepage-module-for-prestashop.html The module's homepage
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_'))
	exit;

class HomeFeaturez extends Module
{
	private $conf = array(
		'HOME_FEATURED_NBR' => 10,
		'HOME_FEATURED_CATALOG' => 1,
		'HOME_FEATURED_RANDOM' => 1,
		'HOME_FEATURED_TITLE' => 1,
		'HOME_FEATURED_DESCR' => 1,
		'HOME_FEATURED_VIEW' => 1,
		'HOME_FEATURED_CART' => 1,
		'HOME_FEATURED_PRICE' => 1,
		'HOME_FEATURED_COLS' => 4,
		'HOME_FEATURED_HEIGHT_ADJUST' => 670, // высота для блока из 4 колонок в 2 ряда для стандартной темы Prestashop
		'HOME_FEATURED_WIDTH_ADJUST' => 535, // ширина для блока из 4 колонок для стандартной темы Prestashop
	);

	public function __construct()
	{
		$this->name = 'homefeaturez';
		$this->tab = version_compare(_PS_VERSION_, '1.4', '>=') ? 'front_office_features' : 'Tools';
		$this->version = '2.3.2';
		$this->author = 'zapalm';
		$this->need_instance = 0;
		$this->bootstrap = false;

		parent::__construct();
		$this->displayName = $this->l('Featured Products on the homepage (zapalm version)');
		$this->description = $this->l('Displays featured products in the middle of the homepage.');
	}

	public function install()
	{
		foreach ($this->conf as $c => $v)
			Configuration::updateValue($c, $v);

		return parent::install() && $this->registerHook('home');
	}

	public function uninstall()
	{
		foreach ($this->conf as $c => $v)
			Configuration::deleteByName($c);

		return parent::uninstall();
	}

	public function getContent()
	{
		global $cookie;

		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submit_save'))
		{
			$res = 1;
			foreach ($this->conf as $c => $v) {
				$res &= Configuration::updateValue($c, intval(Tools::getValue($c)));
			}
			$output .= $res ? $this->displayConfirmation($this->l('Settings updated')) : $this->displayError($this->l('Some setting not updated'));
		}

		$cols = Configuration::getMultiple(array_keys($this->conf));
		$categories = Category::getHomeCategories($cookie->id_lang, false);
		$root_cat = Category::getRootCategory($cookie->id_lang);

		$output .= '		
			<fieldset style="width: 800px;">
				<legend><img src="'._PS_ADMIN_IMG_.'cog.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
					<label>'.$this->l('Number of product displayed').'</label>
					<div class="margin-form">
						<input type="text" size="5" name="HOME_FEATURED_NBR" value="'.($cols['HOME_FEATURED_NBR'] ? $cols['HOME_FEATURED_NBR'] : '10').'" />
						<p class="clear">'.$this->l('The number of products displayed on homepage (default: 10).').'</p>					
					</div>
					<label>'.$this->l('Category of products to display').'</label>
					<div class="margin-form">
						<select name="HOME_FEATURED_CATALOG">
							<option value="'.$root_cat->id.'" '.($cols['HOME_FEATURED_CATALOG'] == $root_cat->id ? 'selected=1' : '').'>'.$root_cat->name.'</option>';
							foreach ($categories as $c)
								$output .= '<option value="'.$c['id_category'].'" '.($cols['HOME_FEATURED_CATALOG'] == $c['id_category'] ? 'selected=1' : '').'>'.$c['name'].'</option>';
							$output .= '
						</select>
						<p class="clear">'.$this->l('Choose category of products, which will show on the homepage (default : Home category).').'</p>						
					</div>				
					<label>'.$this->l('Show products randomly').'</label>
					<div class="margin-form">
						<input type="checkbox" name="HOME_FEATURED_RANDOM" value="1" '.($cols['HOME_FEATURED_RANDOM'] ? 'checked="checked"' : '').' />
						<p class="clear">'.$this->l('Check it, if you want to show products randomly.').'</p>
					</div>								
					<label>'.$this->l('Show title of a product').'</label>
					<div class="margin-form">
						<input type="checkbox" name="HOME_FEATURED_TITLE" value="1" '.($cols['HOME_FEATURED_TITLE'] ? 'checked="checked"' : '').' />
						<p class="clear">'.$this->l('Check it, if you want to show a product title.').'</p>
					</div>	
					<label>'.$this->l('Show description of a product').'</label>
					<div class="margin-form">
						<input type="checkbox" name="HOME_FEATURED_DESCR" value="1" '.($cols['HOME_FEATURED_DESCR'] ? 'checked="checked"' : '').' />
						<p class="clear">'.$this->l('Check it, if you want to show a product description.').'</p>
					</div>	
					<label>'.$this->l('Show a "View" button').'</label>
					<div class="margin-form">
						<input type="checkbox" name="HOME_FEATURED_VIEW" value="1" '.($cols['HOME_FEATURED_VIEW'] ? 'checked="checked"' : '').' />
						<p class="clear">'.$this->l('Check it, if you want to show a "View" button.').'</p>
					</div>		
					<label>'.$this->l('Show a "Add to cart" button').'</label>
					<div class="margin-form">
						<input type="checkbox" name="HOME_FEATURED_CART" value="1" '.($cols['HOME_FEATURED_CART'] ? 'checked="checked"' : '').' />
						<p class="clear">'.$this->l('Check it, if you want to show a "Add to cart" button. If prestashop catalog mode is enable than the button will not display.').'</p>
					</div>
					<label>'.$this->l('Show product price').'</label>
					<div class="margin-form">
						<input type="checkbox" name="HOME_FEATURED_PRICE" value="1" '.($cols['HOME_FEATURED_PRICE'] ? 'checked="checked"' : '').' />
						<p class="clear">'.$this->l('Check it, if you want to show product price.').'</p>
					</div>
					<label>'.$this->l('Number of columns to display').'</label>
					<div class="margin-form">
						<input type="text" size="1" name="HOME_FEATURED_COLS" value="'.($cols['HOME_FEATURED_COLS'] ? $cols['HOME_FEATURED_COLS'] : '4').'" />
						<p class="clear">'.$this->l('The number of columns displayed on homepage (default: 4).').'</p>					
					</div>
					<label>'.$this->l('Block module height adjust').'</label>
					<div class="margin-form">
						<input type="text" size="3" name="HOME_FEATURED_HEIGHT_ADJUST" value="'.($cols['HOME_FEATURED_HEIGHT_ADJUST'] ? $cols['HOME_FEATURED_HEIGHT_ADJUST'] : '0').'" /> px.						
						<p class="clear">'.$this->l('You should input number of pixels to adjust height of the block.').'</p>
					</div>						
					<label>'.$this->l('Block module width adjust').'</label>
					<div class="margin-form">
						<input type="text" size="3" name="HOME_FEATURED_WIDTH_ADJUST" value="'.($cols['HOME_FEATURED_WIDTH_ADJUST'] ? $cols['HOME_FEATURED_WIDTH_ADJUST'] : '0').'" /> px.						
						<p class="clear">'.$this->l('You should input number of pixels to adjust width of the block.').'</p>
					</div>					
					<center><input type="submit" name="submit_save" value="'.$this->l('Save').'" class="button" /></center>
				</form>
			</fieldset>
			<br class="clear" />
		';

		return $output;
	}

	public function hookHome($params)
	{
		global $smarty;

		$conf = Configuration::getMultiple(array_keys($this->conf));
		$conf['HOME_FEATURED_COLS'] = (int)$conf['HOME_FEATURED_COLS'] ? (int)$conf['HOME_FEATURED_COLS'] : 4;
		$cat = (int)$conf['HOME_FEATURED_CATALOG'];
		$category = new Category($cat ? $cat : 1);

		$nb = (int)$this->conf['HOME_FEATURED_NBR'];
		if ($conf['HOME_FEATURED_RANDOM'])
			$products = $category->getProducts(intval($params['cookie']->id_lang), 1, ($nb ? $nb : 10), 'date_add', 'DESC', false, true, true, ($nb ? $nb : 10));
		else
			$products = $category->getProducts(intval($params['cookie']->id_lang), 1, ($nb ? $nb : 10), 'date_add', 'DESC');

		// width in pixels of the home featured block
		$block_width = (int)$conf['HOME_FEATURED_WIDTH_ADJUST'];

		// number of products per line
		$nb_items_per_line = (int)$conf['HOME_FEATURED_COLS'];

		// width in pixels of a product list item
		$block_li_width = ceil($block_width / $nb_items_per_line) - 1;

		// size of a product image
		$pic_size_type = 'home';

		$smarty->assign(array(
			'nb_items_per_line' => $nb_items_per_line,
			'block_li_width' => $block_li_width,
			'products' => $products,
			'pic_size_type' => $pic_size_type,
			'conf' => $conf
		));

		return $this->display(__FILE__, 'homefeaturez.tpl');
	}
}