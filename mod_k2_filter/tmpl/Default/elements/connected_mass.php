	<script type="text/javascript">
		
		jQuery(document).ready(function() {
			
			jQuery('select.child').each(function() {
				if(jQuery(this).siblings().find(".dynoloader").length == 0) {
					jQuery(this).parent().prepend("<div class='dynoloader' style='display: none;'><img src='<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/images/loading.png' style='width: 20px;' /></div>");
				}
			});

			jQuery.urlParam = function(name){
				var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
				if(results) {
					return results[1];
				}
				return '';
			}		
	
			jQuery('#K2FilterBox<?php echo $module->id; ?> select.connected:enabled').each(function() {
				var elem  = jQuery(this);
				var name  = encodeURIComponent(jQuery(this).attr("rel"));
				var value = encodeURIComponent(jQuery(this).find("option:selected").val());
				var index = jQuery(this).find("option:selected").index();

				var next  = jQuery(this).parents('.k2filter-cell').nextAll().find("select.connected").eq(0);
				
				var data = 'name='+name+'&value='+value+'&next='+encodeURIComponent(next.attr("rel")) + '&lang=<?php echo $shortLang; ?>';
				
				<?php if($connected_show_all) : ?>
				if(index == 0) {
					var data_all = 'name='+name+'&value=getall&next='+encodeURIComponent(next.attr("rel")) + '&lang=<?php echo $shortLang; ?>';
					jQuery.ajax({
						data: data_all,
						url: '<?php echo JURI::root(); ?>modules/mod_k2_filter/ajax.php',
						success: function(response) {
							next.append(response);
							next.removeAttr("disabled");
							var get_array = new Array();
							<?php foreach($_GET as $k=>$val) : ?>
							get_array.push("<?php echo $k; ?>");
							<?php endforeach; ?>

							next.find("option").each(function() {
								if(jQuery(this).attr("data-extra-id") !== undefined) {
									var param = 'searchword' + jQuery(this).attr("data-extra-id");
									if(jQuery.inArray(param, get_array) != -1) {
										var get_var = decodeURIComponent(jQuery.urlParam(param).replace(/\+/g, " "));
										if(jQuery(this).text() == get_var) {
											jQuery(this).attr('selected', 'selected');
										}
									}
								}
							});
						}
					});
				}
				<?php endif; ?>

				if(index != 0) {
					jQuery.ajax({
						data: data,
						url: '<?php echo JURI::root(); ?>modules/mod_k2_filter/ajax.php',
						success: function(response) {
							next.append(response);
							next.removeAttr("disabled");

							if(jQuery(response).filter("option").length != 0) {
								var nextId = next.find("option:last-child").text();
								next.find("option:last-child").remove();
							}
							if(nextId) {
								next.attr("name", "searchword"+nextId);
							}
							
							var param = 'searchword'+nextId;
							var get_var = decodeURIComponent(jQuery.urlParam(param).replace(/\+/g, " "));

							if(get_var != 'null') {
								next.find("option").each(function() {
									if(jQuery(this).text() == get_var) {
										jQuery(this).attr('selected', 'selected');
									}
								});
								
								next_checker  = next.parents('.k2filter-cell').next().find("select");
								if(next_checker.length == 0) {
									next_checker =  next.parents('.k2filter-row').next().find('.k2filter-cell:eq(0)').find("select");
								}

								if(next_checker.length != 0) {
									elem  = next;
									name  = encodeURIComponent(next.attr("rel"));
									value = encodeURIComponent(next.find("option:selected").val());
									index = next.find("option:selected").index();
									
									next  = next.parents('.k2filter-cell').nextAll().find("select.connected").eq(0);
									
									data = 'name='+name+'&value='+value+'&next='+encodeURIComponent(next.attr("rel")) + '&lang=<?php echo $shortLang; ?>';
							
									jQuery.ajax({
										data: data,
										url: '<?php echo JURI::root(); ?>modules/mod_k2_filter/ajax.php',
										success: function(response) {
											next.append(response);
											next.removeAttr("disabled");

											if(jQuery(response).filter("option").length != 0) {
												var nextId = next.find("option:last-child").text();
												next.find("option:last-child").remove();
											}
											if(nextId) {
												next.attr("name", "searchword"+nextId);
											}
											
												param = 'searchword'+nextId;
												get_var = decodeURIComponent(jQuery.urlParam(param).replace(/\+/g, " "));
												
												if(get_var != 'null') {
													next.find("option").each(function() {
														if(jQuery(this).text() == get_var) {
															jQuery(this).attr('selected', 'selected');
														}
													});
													
													next_checker  = next.parents('.k2filter-cell').next().find("select");
													if(next_checker.length == 0) {
														next_checker =  next.parents('.k2filter-row').next().find('.k2filter-cell:eq(0)').find("select");
													}

													if(next_checker.length != 0) {
														elem  = next;
														name  = encodeURIComponent(next.attr("rel"));
														value = encodeURIComponent(next.find("option:selected").val());
														index = next.find("option:selected").index();
														
														next  = next.parents('.k2filter-cell').nextAll().find("select.connected").eq(0);
														
														data = 'name='+name+'&value='+value+'&next='+encodeURIComponent(next.attr("rel")) + '&lang=<?php echo $shortLang; ?>';
													
														jQuery.ajax({
															data: data,
															url: '<?php echo JURI::root(); ?>modules/mod_k2_filter/ajax.php',
															success: function(response) {
																next.append(response);
																next.removeAttr("disabled");

																if(jQuery(response).filter("option").length != 0) {
																	var nextId = next.find("option:last-child").text();
																	next.find("option:last-child").remove();
																}
																if(nextId) {
																	next.attr("name", "searchword"+nextId);
																}
																
																	param = 'searchword'+nextId;
																	get_var = decodeURIComponent(jQuery.urlParam(param).replace(/\+/g, " "));
																	
																	if(get_var != 'null') {
																		next.find("option").each(function() {
																			if(jQuery(this).text() == get_var) {
																				jQuery(this).attr('selected', 'selected');
																			}
																		});
																	}
															}
														});
													};
												}
										}
									});
								};
							};
						}
					});
				}
			});
			
			jQuery('#K2FilterBox<?php echo $module->id; ?> select.connected').change(function() {
				var elem  = jQuery(this);
				var name  = encodeURIComponent(jQuery(this).attr("rel"));
				var value = encodeURIComponent(jQuery(this).find("option:selected").val());

				var index = jQuery(this).find("option:selected").index();
				
				var next  = jQuery(this).parents('.k2filter-cell').nextAll().find("select.connected").eq(0);
				
				next.parent().find('.dynoloader').show();
				
				<?php if($connected_show_all) : ?>
				var extra_id = jQuery(this).find("option:selected").attr('data-extra-id');
				if(extra_id !== undefined) {
					elem.attr("name", "searchword" + extra_id);
				}
				<?php endif; ?>
				
				if(!next.hasClass('connected') || jQuery(this).hasClass('lastchild')) {
					return;
				}
				
				var data = 'name='+name+'&value='+value+'&next='+encodeURIComponent(next.attr("rel")) + '&lang=<?php echo $shortLang; ?>';
				
				//disable all next selects
				var elemIndex = jQuery('#K2FilterBox<?php echo $module->id; ?> select.connected').index(this);
				var nextAll  = jQuery(this).parents('#K2FilterBox<?php echo $module->id; ?>').find('select.connected:gt('+elemIndex+')');
				
				nextAll.each(function() {
					jQuery(this).attr("disabled", 'disabled');
					jQuery(this).find("option").not(':eq(0)').remove();
					if(jQuery(this).hasClass("lastchild")) {
						return;
					}
				});
				
				if(index == 0) {
					<?php if($connected_show_all) : ?>
					var data_all = 'name='+name+'&value=getall&next='+encodeURIComponent(next.attr("rel")) + '&lang=<?php echo $shortLang; ?>';
					jQuery.ajax({
						data: data_all,
						url: '<?php echo JURI::root(); ?>modules/mod_k2_filter/ajax.php',
						success: function(response) {
							next.append(response);
							next.removeAttr("disabled");
							next.parent().find('.dynoloader').hide();
						}
					});
					return;
					<?php else : ?>
					next.parent().find('.dynoloader').hide();
					return;
					<?php endif; ?>
				}

				jQuery.ajax({
					data: data,
					url: '<?php echo JURI::root(); ?>modules/mod_k2_filter/ajax.php',
					success: function(response) {
						next.find('option').not(':eq(0)').remove();
						next.append(response);
						next.removeAttr("disabled");
						next.parent().find('.dynoloader').hide();

						if(jQuery(response).filter("option").length != 0) {
							var nextId = next.find("option:last-child").text();
							next.find("option:last-child").remove();
						}
						if(nextId) {
							next.attr("name", "searchword"+nextId);
						}
						else {
							next.removeAttr("name");
						}
					}
				});
				
				<?php if($acounter) : ?>
					jQuery("#K2FilterBox<?php echo $module->id; ?> div.acounter .data").hide();
					jQuery("#K2FilterBox<?php echo $module->id; ?> div.acounter .loader").show();
					jQuery.ajax({
						url: jQuery("#K2FilterBox<?php echo $module->id; ?> form:eq(0)").attr("action"),
						data: jQuery("#K2FilterBox<?php echo $module->id; ?> form:eq(0)").serialize() + "&format=count",
						type: jQuery("#K2FilterBox<?php echo $module->id; ?> form:eq(0)").attr('method'),
						success: function(response) {
							jQuery("#K2FilterBox<?php echo $module->id; ?> div.acounter .loader").hide();
							jQuery("#K2FilterBox<?php echo $module->id; ?> div.acounter .data").html("<p>"+response+" <?php echo JText::_("MOD_K2_FILTER_ACOUNTER_TEXT"); ?></p>").show();
						}
					});
					<?php if($onchange) : ?>
					submit_form_<?php echo $module->id; ?>();
					<?php endif; ?>
				
				<?php endif; ?>
				
				return;
			});
		
		});
	
	</script>