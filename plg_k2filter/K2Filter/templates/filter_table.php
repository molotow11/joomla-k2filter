<?php
/**
 * @version		$Id: generic.php 1492 2012-02-22 17:40:09Z joomlaworks@gmail.com $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
$moduleId = JRequest::getInt("moduleId");
$moduleParams = modK2FilterHelper::getModuleParams($moduleId);

//extrafields exclude
$exclude_extra = $moduleParams->ordering_extra_exclude;	
$exclude_extra = str_replace(" ", "", $exclude_extra);
if($exclude_extra != "") {
	$exclude_extra = explode(",", $exclude_extra);
}

?>

<style>

/* Adminlist grids */

table.adminlist {
	width: 100%;
	border-spacing: 1px;
	background-color: #f3f3f3;
	color: #666;
}

table.adminlist td,
table.adminlist th {
	padding: 4px;
}

table.adminlist td {padding-left: 8px;}

table.adminlist thead th {
	text-align: center;
	background: #f7f7f7;
	color: #666;
	border-bottom: 1px solid #CCC;
	border-left: 1px solid #fff;
	padding: 10px;
}

table.adminlist thead th.left {
	text-align: left;
}

table.adminlist thead a:hover {
	text-decoration: none;
}

table.adminlist thead th img {
	vertical-align: middle;
	padding-left: 3px;
}

table.adminlist tbody th {
	font-weight: bold;
}

table.adminlist tbody tr {
	background-color: #fff;
	text-align: left;
}

table.adminlist tbody tr.row0:hover td,
table.adminlist tbody tr.row1:hover td	{
	background-color: #e8f6fe;
}

table.adminlist tbody tr td {
	background: #fff;
	border: 1px solid #fff;
	padding: 10px;
}

table.adminlist tbody tr.row1 td {
	background: #f0f0f0;
	border-top: 1px solid #FFF;
}

table.adminlist tfoot tr {
	text-align: center;
	color: #333;
}

table.adminlist tfoot td,table.adminlist tfoot th {
	background-color: #f7f7f7;
	border-top: 1px solid #999;
	text-align: center;
}

table.adminlist td.order {
	text-align: center;
	white-space: nowrap;
	width: 200px;
}

table.adminlist td.order span {
	float: left;
	width: 20px;
	text-align: center;
	background-repeat: no-repeat;
	height: 13px;
}

table.adminlist .pagination {
	display: inline-block;
	padding: 0;
	margin: 0 auto;
}

</style>

<script>
	
	jQuery(document).ready(function() {

		jQuery("div.genericItemList th a").click(function() {
			var value = jQuery(this).attr("rel");
			document.K2Filter<?php echo $moduleId; ?>.orderby.value = value;

			if(jQuery(this).hasClass("desc")) {
				document.K2Filter<?php echo $moduleId; ?>.orderto.value='asc'
			}
			else {
				document.K2Filter<?php echo $moduleId; ?>.orderto.value='desc'
			}

			submit_form_<?php echo $moduleId; ?>();
			
			return false;
		});
	
	});

</script>

<!-- Start K2 Generic (search/date) Layout -->
<div id="k2Container" class="genericView<?php if($this->params->get('pageclass_sfx')) echo ' '.$this->params->get('pageclass_sfx'); ?>">

	<?php if($this->params->get('show_page_title')): ?>
	<!-- Page title -->
	<div class="componentheading<?php echo $this->params->get('pageclass_sfx')?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
	<?php endif; ?>

	<?php if($this->params->get('genericFeedIcon',1)): ?>
	<!-- RSS feed icon -->
	<div class="k2FeedIcon">
		<a href="<?php echo $this->feed; ?>" title="<?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?>">
			<span><?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?></span>
		</a>
		<div class="clr"></div>
	</div>
	<?php endif; ?>
	
	<!--added K2FSM -->
	<div class="k2filter-toolbox" style="margin: 20px 0;">
		<?php
			require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
			$moduleId = JRequest::getInt("moduleId");
			$moduleParams = modK2FilterHelper::getModuleParams($moduleId);
		?>
		
		<?php if($this->resultf != "") : ?>
		<h4 class="resultf" style="display: inline-block;">
			<?php echo JText::_($this->resultf); ?>
			<?php if($this->result_count != 0) : ?>
			<?php echo "(".$this->result_count.")" ?>
			<?php endif; ?>
		</h4>
		<?php endif; ?>
		
		<?php if($moduleParams->template_selector == 1 && $this->result_count != 0) : ?>
		<span class="template_selector" style="float: right;">
			<a href="#" onclick="document.K2Filter<?php echo $moduleId; ?>.template_id.value='0'; submit_form_<?php echo $moduleId; ?>(); return false;"><img src="<?php echo JURI::base(); ?>modules/mod_k2_filter/assets/generic.png" /></a>
			<a href="#" onclick="document.K2Filter<?php echo $moduleId; ?>.template_id.value='1'; submit_form_<?php echo $moduleId; ?>(); return false;"><img src="<?php echo JURI::base(); ?>modules/mod_k2_filter/assets/generic_table.png" /></a>
		</span>
		<?php endif; ?>
		
		<div style="clear: both;"></div>
		
		<?php if($moduleParams->ordering == 1 && $this->result_count != 0) : ?>
		<div class="ordering-box" style="display: inline-block;">
			<select style="display: inline-block; width: auto;" name="orderby" onChange="document.K2Filter<?php echo $moduleId; ?>.orderby.value=this.value; submit_form_<?php echo $moduleId; ?>();">
				
				<?php $filterLang =& JFactory::getLanguage();
					  $filterLang->load("mod_k2_filter");
				?>
			
				<option value=""><?php echo JText::_("MOD_K2_FILTER_ORDERING_VALUE"); ?></option>

				<option value="date" <?php if (JRequest::getVar('orderby') == "date") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "date") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_DATE'); ?></option>
				<option value="alpha" <?php if (JRequest::getVar('orderby') == "alpha") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "alpha") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_TITLE'); ?></option>
				<option value="order" <?php if (JRequest::getVar('orderby') == "order") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "order") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_ORDER'); ?></option>
				<option value="featured" <?php if (JRequest::getVar('orderby') == "featured") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "featured") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_FEATURED'); ?></option>
				<option value="hits" <?php if (JRequest::getVar('orderby') == "hits") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "hits") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_HITS'); ?></option>
				<option value="rand" <?php if (JRequest::getVar('orderby') == "rand") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "rand") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_RANDOM'); ?></option>
				<option value="best" <?php if (JRequest::getVar('orderby') == "best") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "best") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_RATING'); ?></option>
				<option value="id" <?php if (JRequest::getVar('orderby') == "id") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "id") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_ID'); ?></option>	
				
				<?php if($moduleParams->ordering_extra == 1) : ?>
					<?php foreach($this->extras as $extra) : ?>
					
						<option value="<?php echo $extra->id;?>" <?php if(JRequest::getVar("orderby") == $extra->id) echo "selected=selected"; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == $extra->id) echo "selected=selected"; ?>>
							<?php echo $extra->name; ?>
						</option>
					
					<?php endforeach; ?>
				<?php endif; ?>
				
				<option value="k2store" <?php if (JRequest::getVar('orderby') == "k2store") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "k2store") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_ORDERING_K2STORE'); ?></option>
				<option value="j2store" <?php if (JRequest::getVar('orderby') == "j2store") echo 'selected="selected"'; ?><?php if(JRequest::getVar("orderby") == '' && $moduleParams->ordering_default == "j2store") echo "selected=selected"; ?>><?php echo JText::_('MOD_K2_FILTER_FILTER_TYPE_PRICE_RANGE_J2STORE'); ?></option>

			</select>
			
			<?php $order_method = JRequest::getVar("orderto", ""); ?>
			
			<?php if($order_method != "") : ?>
				<?php if($order_method == "desc") : ?>
					<a href="#" onclick="document.K2Filter<?php echo $moduleId; ?>.orderto.value='asc'; submit_form_<?php echo $moduleId; ?>(); return false;">
						<img src="<?php echo JURI::base(); ?>modules/mod_k2_filter/assets/sort_desc.png" border="0" alt="<?php echo JText::_('MOD_K2_FILTER_ORDERING_DESC'); ?>" title="<?php echo JText::_('MOD_K2_FILTER_ORDERING_DESC'); ?>" width="12" height="12" />
					</a>		
				<?php else : ?>
					<a href="#" onclick="document.K2Filter<?php echo $moduleId; ?>.orderto.value='desc'; submit_form_<?php echo $moduleId; ?>(); return false;">
						<img src="<?php echo JURI::base(); ?>modules/mod_k2_filter/assets/sort_asc.png" border="0" alt="<?php echo JText::_('MOD_K2_FILTER_ORDERING_ASC'); ?>" title="<?php echo JText::_('MOD_K2_FILTER_ORDERING_ASC'); ?>" width="12" height="12">
					</a>
				<?php endif; ?>
			<?php else : ?>
				<?php $order_method = $moduleParams->ordering_default_method; ?>

				<?php if($order_method == "desc") : ?>
					<a href="#" onclick="document.K2Filter<?php echo $moduleId; ?>.orderto.value='asc'; submit_form_<?php echo $moduleId; ?>(); return false;">
						<img src="<?php echo JURI::base(); ?>modules/mod_k2_filter/assets/sort_desc.png" border="0" alt="<?php echo JText::_('MOD_K2_FILTER_ORDERING_DESC'); ?>" title="<?php echo JText::_('MOD_K2_FILTER_ORDERING_DESC'); ?>" width="12" height="12" />
					</a>		
				<?php else : ?>
					<a href="#" onclick="document.K2Filter<?php echo $moduleId; ?>.orderto.value='desc'; submit_form_<?php echo $moduleId; ?>(); return false;">
						<img src="<?php echo JURI::base(); ?>modules/mod_k2_filter/assets/sort_asc.png" border="0" alt="<?php echo JText::_('MOD_K2_FILTER_ORDERING_ASC'); ?>" title="<?php echo JText::_('MOD_K2_FILTER_ORDERING_ASC'); ?>" width="12" height="12">
					</a>
				<?php endif; ?>
			<?php endif; ?>
			
		</div>
		<select style="float: right; width: 80px;" name="flimit" class="limit-box" onChange="document.K2Filter<?php echo $moduleId; ?>.flimit.value=this.value; submit_form_<?php echo $moduleId; ?>();">
			<?php 
				$limits = Array(5, 10, 15, 20, 30, 50, 100, 9999); 
				$selected = JRequest::getInt('flimit');
			?>
			<option value=""><?php echo JText::_("Show"); ?></option>
			<?php foreach($limits as $limit) { ?>
			<option value="<?php echo $limit; ?>"<?php if ($selected == $limit) echo ' selected="selected"'; ?>><?php echo $limit == 9999 ? "All" : $limit; ?></option>
			<?php } ?>
		</select>
		<?php endif; ?>
		<div style="clear: both;"></div>
	</div>	
	<!--///added K2FSM -->

	<?php if(count($this->items)): ?>
	<div class="genericItemList K2AdminViewItems">
		
		<table cellpadding="0" cellspacing="0" border="0" class="adminlist">
		
		<thead>
			<tr>
				<?php if($this->params->get('genericItemImage')) { ?>
				<th><a href="#" rel="alpha"><?php echo JText::_('Image'); ?></a></th>
				<?php } ?>
				<?php if($this->params->get('genericItemDateCreated')) : ?>
				<th>
					<a href="#" rel="date" title="<?php echo JText::_("JGLOBAL_CLICK_TO_SORT_THIS_COLUMN"); ?>"<?php if(JRequest::getVar("orderby") == "date" && JRequest::getVar("orderto") == "desc") echo " class='desc'"; ?>>
						<?php echo JText::_("K2_DATE"); ?>
						
						<?php if(JRequest::getVar("orderby") == "date") : ?>
							<?php if(JRequest::getVar("orderto") == "desc") : ?>
								<img src="modules/mod_k2_filter/assets/sort_desc.png" border="0" width="12" height="12" />
							<?php else : ?>
								<img src="modules/mod_k2_filter/assets/sort_asc.png" border="0" width="12" height="12" />
							<?php endif; ?>
						<?php endif; ?>
						
					</a>
				</th>
				<?php endif; ?>
				
				<?php if($this->params->get('genericItemCategory')) : ?>
				<th>
					<a href="#" rel="cat" title="<?php echo JText::_("JGLOBAL_CLICK_TO_SORT_THIS_COLUMN"); ?>"<?php if(JRequest::getVar("orderby") == "cat" && JRequest::getVar("orderto") == "desc") echo " class='desc'"; ?>>
						<?php echo JText::_("K2_PUBLISHED_IN"); ?>
						
						<?php if(JRequest::getVar("orderby") == "cat") : ?>
							<?php if(JRequest::getVar("orderto") == "desc") : ?>
								<img src="modules/mod_k2_filter/assets/sort_desc.png" border="0" width="12" height="12" />
							<?php else : ?>
								<img src="modules/mod_k2_filter/assets/sort_asc.png" border="0" width="12" height="12" />
							<?php endif; ?>
						<?php endif; ?>
					</a>
				</th>
				<?php endif; ?>
				
				<?php if($this->params->get('genericItemTitle')) : ?>
				<th>
					<a href="#" rel="alpha" title="<?php echo JText::_("JGLOBAL_CLICK_TO_SORT_THIS_COLUMN"); ?>"<?php if(JRequest::getVar("orderby") == "alpha" && JRequest::getVar("orderto") == "desc") echo " class='desc'"; ?>>
						<?php echo JText::_("K2_NAME"); ?>
						
						<?php if(JRequest::getVar("orderby") == "alpha") : ?>
							<?php if(JRequest::getVar("orderto") == "desc") : ?>
								<img src="modules/mod_k2_filter/assets/sort_desc.png" border="0" width="12" height="12" />
							<?php else : ?>
								<img src="modules/mod_k2_filter/assets/sort_asc.png" border="0" width="12" height="12" />
							<?php endif; ?>
						<?php endif; ?>
					</a>
				</th>
				<?php endif; ?>
				
				<?php if($this->params->get('genericItemIntroText')) : ?>
				<th>
					<a href="#" rel="intro" title="<?php echo JText::_("JGLOBAL_CLICK_TO_SORT_THIS_COLUMN"); ?>"<?php if(JRequest::getVar("orderby") == "intro" && JRequest::getVar("orderto") == "desc") echo " class='desc'"; ?>>
						<?php echo JText::_("K2_DESCRIPTION"); ?>
						
						<?php if(JRequest::getVar("orderby") == "intro") : ?>
							<?php if(JRequest::getVar("orderto") == "desc") : ?>
								<img src="modules/mod_k2_filter/assets/sort_desc.png" border="0" width="12" height="12" />
							<?php else : ?>
								<img src="modules/mod_k2_filter/assets/sort_asc.png" border="0" width="12" height="12" />
							<?php endif; ?>
						<?php endif; ?>
					</a>
				</th>
				<?php endif; ?>
				
				<?php if($this->params->get('genericItemExtraFields')) : ?>
				<?php foreach($this->extras as $extra) : ?>
				<th>
					<a href="#" rel="<?php echo $extra->id; ?>" title="<?php echo JText::_("JGLOBAL_CLICK_TO_SORT_THIS_COLUMN"); ?>"<?php if(JRequest::getVar("orderby") == $extra->id && JRequest::getVar("orderto") == "desc") echo " class='desc'"; ?>>
						<?php echo $extra->name; ?>
						
						<?php if(JRequest::getVar("orderby") == $extra->id) : ?>
							<?php if(JRequest::getVar("orderto") == "desc") : ?>
								<img src="modules/mod_k2_filter/assets/sort_desc.png" border="0" width="12" height="12" />
							<?php else : ?>
								<img src="modules/mod_k2_filter/assets/sort_asc.png" border="0" width="12" height="12" />
							<?php endif; ?>
						<?php endif; ?>
					</a>
				</th>
				<?php endforeach; ?>
				<?php endif; ?>
			</tr>
		</thead>
		
		<tbody>
	
		<?php foreach($this->items as $k=>$item): ?>

			<!-- Start K2 Item Layout -->
			
			<tr class="row<?php echo ($k+2) % 2; ?>">
			
				<?php if($this->params->get('genericItemImage')) { ?>
				<td>
					<?php if(!empty($item->imageGeneric)): ?>
					  <!-- Item Image -->
					  <div class="genericItemImageBlock">
						  <span class="genericItemImage">
							<a href="<?php echo $item->link; ?>" title="<?php if(!empty($item->image_caption)) echo K2HelperUtilities::cleanHtml($item->image_caption); else echo K2HelperUtilities::cleanHtml($item->title); ?>">
								<img src="<?php echo $item->imageGeneric; ?>" alt="<?php if(!empty($item->image_caption)) echo K2HelperUtilities::cleanHtml($item->image_caption); else echo K2HelperUtilities::cleanHtml($item->title); ?>" style="width:<?php echo $this->params->get('itemImageGeneric'); ?>px; height:auto;" />
							</a>
						  </span>
						  <div class="clr"></div>
					  </div>
					<?php endif; ?>
				</td>
				<?php } ?>
			
				<?php if($this->params->get('genericItemDateCreated')): ?>
				<!-- Date created -->
				<td><?php echo JHTML::_('date', $item->created , JText::_('K2_DATE_FORMAT_LC2')); ?></td>
				<?php endif; ?>
			
				<?php if($this->params->get('genericItemCategory')): ?>
				<!-- Item category name -->
				<td>
					<a href="<?php echo $item->category->link; ?>">
						<?php echo $item->category->name; ?>
					</a>
				</td>
				<?php endif; ?>
				
				<?php if($this->params->get('genericItemTitle')): ?>
				<!-- Item title -->
				<td><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></td>
				<?php endif; ?>
 
				<?php if($this->params->get('genericItemIntroText')): ?>
				<!-- Item introtext -->
				<td><?php echo $item->introtext; ?></td>
				<?php endif; ?>
			  
				<?php if($this->params->get('genericItemExtraFields')): ?>
				<!-- Item extra fields -->  				
				<?php foreach($this->extras as $extra) : ?>
					<td>
					<?php foreach ($item->extra_fields as $key=>$extraField) : ?>
						<?php if($extra->id == $extraField->id && !in_array($extraField->id, $exclude_extra)) : ?>
							<?php echo $extraField->value; ?>	
						<?php endif; ?>
					<?php endforeach; ?>	
					</td>
				<?php endforeach; ?>
				<?php endif; ?>
			  
			</tr>

			<!-- End K2 Item Layout -->
		
		<?php endforeach; ?>
		
		</tbody>
		</table>
		
	</div>

	<!-- Pagination -->
	<?php if($this->pagination->getPagesLinks()): ?>
	<div class="k2Pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
		<div class="clr"></div>
		<?php echo $this->pagination->getPagesCounter(); ?>
	</div>
	<?php endif; ?>

	<?php endif; ?>
	
</div>
<!-- End K2 Generic (search/date) Layout -->
