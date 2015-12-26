<!-- MODULE Home Featurez Products -->
{if empty($products)}
	<p>{l s='No featured products' mod='homefeaturez'}</p>
{else}
	<div id="featured-products_block_center" class="block products_block">
		<h4>{l s='Featured products' mod='homefeaturez'}</h4>
		<div class="block_content">
				<ul style="height:{$conf.HOME_FEATURED_HEIGHT_ADJUST|intval}px;">
				{foreach from=$products item=product name=homefeaturezProducts}
					<li style="width: {$block_li_width|intval}px" class="ajax_block_product {if $smarty.foreach.homefeaturezProducts.first}first_item{elseif $smarty.foreach.homefeaturezProducts.last}last_item{else}item{/if} {if $smarty.foreach.homefeaturezProducts.iteration%$nb_items_per_line == 0}last_item_of_line{elseif $smarty.foreach.homefeaturezProducts.iteration%$nb_items_per_line == 1}clear{/if} {if $smarty.foreach.homefeaturezProducts.iteration > ($smarty.foreach.homefeaturezProducts.total - ($smarty.foreach.homefeaturezProducts.total % $nb_items_per_line))}last_line{/if}">
						{if $conf.HOME_FEATURED_TITLE}
							<h5>
								<a href="{$product.link}" title="{$product.name}">
									{$product.name|truncate:29:'...'|escape:'htmlall':'UTF-8'}
								</a>
							</h5>
						{/if}
						{if $conf.HOME_FEATURED_DESCR}
							<div class="product_desc">
								<a href="{$product.link}" title="{l s='More' mod='homefeaturez'}">
									{$product.description_short|strip_tags|truncate:130:'...'}
								</a>
							</div>
						{/if}
						<a href="{$product.link}" title="{$product.name|escape:html:'UTF-8'}" class="product_image" style="background-image: url({$link->getImageLink($product.link_rewrite, $product.id_image, $pic_size_type)}); background-position: center center; background-repeat: no-repeat; background-size: contain; width: {$block_li_width-4}px;"></a>
						
						{if $conf.HOME_FEATURED_PRICE}
							<p class="price_container">
								<span class="price">
									{if !$priceDisplay}
										{convertPrice price=$product.price}
									{else}
										{convertPrice price=$product.price_tax_exc}
									{/if}
								</span>
							</p>
						{/if}
						{if $conf.HOME_FEATURED_VIEW}
							<a class="button" href="{$product.link}" title="{l s='View' mod='homefeaturez'}">
								{l s='View' mod='homefeaturez'}
							</a>
						{/if}

						{if $conf.HOME_FEATURED_CART}
							{if ($product.quantity > 0 OR $product.allow_oosp) AND ($product.customizable != 2)}
								<a class="exclusive ajax_add_to_cart_button" rel="ajax_id_product_{$product.id_product}" href="{$base_dir}cart.php?qty=1&amp;id_product={$product.id_product}&amp;token={$static_token}&amp;add" title="{l s='Add to cart' mod='homefeaturez'}">
									{l s='Add to cart' mod='homefeaturez'}
								</a>
							{else}
								<span class="exclusive">{l s='Add to cart' mod='homefeaturez'}</span>
							{/if}
						{/if}
					</li>
				{/foreach}
				</ul>
			</div>
		</div>
{/if}
<!-- /MODULE Home Featurez Products -->
