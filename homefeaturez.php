<?php
/**
 * Enhanced featured products on homepage: the module for PrestaShop.
 *
 * @author    Maksim T. <zapalm@yandex.com>
 * @copyright 2010 Maksim T.
 * @link      https://prestashop.modulez.ru/en/frontend-features/16-enhanced-featured-products-on-homepage-module-for-prestashop.html The module's homepage
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_'))
	exit;

/**
 * Module HomeFeaturez.
 *
 * @author Maksim T. <zapalm@yandex.com>
 */
class HomeFeaturez extends Module
{
    /** The product ID of the module on its homepage. */
    const HOMEPAGE_PRODUCT_ID = 16;

    /** @var array Default settings. */
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

    /**
     * @inheritdoc
     *
     * @author Maksim T. <zapalm@yandex.com>
     */
	public function __construct()
	{
		$this->name = 'homefeaturez';
		$this->tab = version_compare(_PS_VERSION_, '1.4', '>=') ? 'front_office_features' : 'Tools';
		$this->version = '2.3.2';
		$this->author = 'zapalm';
		$this->need_instance = false;

		parent::__construct();
		$this->displayName = $this->l('Featured Products on the homepage (zapalm version)');
		$this->description = $this->l('Displays featured products in the middle of the homepage.');
	}

    /**
     * @inheritdoc
     *
     * @author Maksim T. <zapalm@yandex.com>
     */
    public function install()
    {
        $result = parent::install();

        if ($result) {
            foreach ($this->conf as $c => $v) {
                Configuration::updateValue($c, $v);
            }

            $result = $this->registerHook('home');
        }

        $this->registerModuleOnQualityService('installation');

        return (bool)$result;
    }

    /**
     * @inheritdoc
     *
     * @author Maksim T. <zapalm@yandex.com>
     */
    public function uninstall()
    {
        $result = (bool)parent::uninstall();

        if ($result) {
            foreach ($this->conf as $c => $v) {
                Configuration::deleteByName($c);
            }
        }

        $this->registerModuleOnQualityService('uninstallation');

        return $result;
    }

    /**
     * @inheritdoc
     *
     * @author Maksim T. <zapalm@yandex.com>
     */
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

        // The block about the module (version: 2021-08-19)
        $modulezUrl    = 'https://prestashop.modulez.ru' . (Language::getIsoById(false === empty($GLOBALS['cookie']->id_lang) ? $GLOBALS['cookie']->id_lang : Context::getContext()->language->id) === 'ru' ? '/ru/' : '/en/');
        $modulePage    = $modulezUrl . self::HOMEPAGE_PRODUCT_ID . '-' . $this->name . '.html';
        $licenseTitle  = 'Academic Free License (AFL 3.0)';
        $output       .=
            (version_compare(_PS_VERSION_, '1.6', '<') ? '<br class="clear" />' : '') . '
            <div class="panel">
                <div class="panel-heading">
                    <img src="' . $this->_path . 'logo.gif" width="16" height="16" alt=""/>
                    ' . $this->l('Module info') . '
                </div>
                <div class="form-wrapper">
                    <div class="row">               
                        <div class="form-group col-lg-4" style="display: block; clear: none !important; float: left; width: 33.3%;">
                            <span><b>' . $this->l('Version') . ':</b> ' . $this->version . '</span><br/>
                            <span><b>' . $this->l('License') . ':</b> ' . $licenseTitle . '</span><br/>
                            <span><b>' . $this->l('Website') . ':</b> <a class="link" href="' . $modulePage . '" target="_blank">prestashop.modulez.ru</a></span><br/>
                            <span><b>' . $this->l('Author') . ':</b> ' . $this->author . '</span><br/><br/>
                        </div>
                        <div class="form-group col-lg-2" style="display: block; clear: none !important; float: left; width: 16.6%;">
                            <img width="250" alt="' . $this->l('Website') . '" src="https://prestashop.modulez.ru/img/marketplace-logo.png" />
                        </div>
                    </div>
                </div>
            </div> ' .
            (version_compare(_PS_VERSION_, '1.6', '<') ? '<br class="clear" />' : '') . '
        ';

		return $output;
	}

    /**
     * @inheritdoc
     *
     * @author Maksim T. <zapalm@yandex.com>
     */
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

    /**
     * Registers current module installation/uninstallation in the quality service.
     *
     * This method is needed for a developer to quickly find out about a problem with installing or uninstalling a module.
     *
     * @param string $operation The operation. Possible values: installation, uninstallation.
     *
     * @author Maksim T. <zapalm@yandex.com>
     */
    private function registerModuleOnQualityService($operation)
    {
        @file_get_contents('https://prestashop.modulez.ru/scripts/quality-service/index.php?' . http_build_query([
            'data' => json_encode([
                'productId'           => self::HOMEPAGE_PRODUCT_ID,
                'productSymbolicName' => $this->name,
                'productVersion'      => $this->version,
                'operation'           => $operation,
                'status'              => (empty($this->_errors) ? 'success' : 'error'),
                'message'             => (false === empty($this->_errors) ? strip_tags(stripslashes(implode(' ', (array)$this->_errors))) : ''),
                'prestashopVersion'   => _PS_VERSION_,
                'thirtybeesVersion'   => (defined('_TB_VERSION_') ? _TB_VERSION_ : ''),
                'shopDomain'          => (method_exists('Tools', 'getShopDomain') && Tools::getShopDomain() ? Tools::getShopDomain() : (Configuration::get('PS_SHOP_DOMAIN') ? Configuration::get('PS_SHOP_DOMAIN') : Tools::getHttpHost())),
                'shopEmail'           => Configuration::get('PS_SHOP_EMAIL'), // This public e-mail from a shop's contacts can be used by a developer to send only an urgent information about security issue of a module!
                'phpVersion'          => PHP_VERSION,
                'ioncubeVersion'      => (function_exists('ioncube_loader_iversion') ? ioncube_loader_iversion() : ''),
                'languageIsoCode'     => Language::getIsoById(false === empty($GLOBALS['cookie']->id_lang) ? $GLOBALS['cookie']->id_lang : Context::getContext()->language->id),
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]));
    }
}