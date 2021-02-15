<div class="tg_premium_backend_main_box">
  <h1>Get more features</h1>
  <p><?php printf( 'Upgrade <b>Tag Groups</b> to get many more features that take your tags to the next level:', 'href="https://chattymango.com/tag-groups-premium/?pk_campaign=tg&pk_kwd=dashboard" target="_blank"' ) ?></p>
  <ul style="list-style:disc;">
    <li style="padding:0 1em; margin-left:1em;">The <b>Shuffle Box</b>, a filterable tag cloud: Filter tags by group or by name with a nifty animation. See the image below.</li>
    <li style="padding:0 1em; margin-left:1em;"><b>Toggle Post Filter</b>: Turn your tags into toggles and let your visitors search live for matching posts by any tag combination and by text.</li>
    <li style="padding:0 1em; margin-left:1em;">A <b>tag input tool</b> on the post edit screen allows you to work with tags on two levels: first select the group, and then choose among the tags of that group.</li>
    <li style="padding:0 1em; margin-left:1em;"><b>Color coding</b> minimizes the risk of accidentally creating a new tag with a typo: New tags are green, tags that changed their groups are yellow.</li>
    <li style="padding:0 1em; margin-left:1em;"><b>Control new tags:</b> Optionally restrict the creation of new tags or prevent moving tags to another group on the post edit screen. These restrictions can be overridden per user role.</li>
    <li style="padding:0 1em; margin-left:1em;"><b>Bulk-add tags:</b> If you often need to insert the same set of tags, simply join them in one group and insert them with the push of a button.</li>
    <li style="padding:0 1em; margin-left:1em;">The option to add each tag to <b>multiple groups</b>.</li>
    <li style="padding:0 1em; margin-left:1em;"><b>Filter posts</b> on the front end by tag group through a URL parameter.</li>
    <li style="padding:0 1em; margin-left:1em;">Display <b>post tags</b> segmented into groups under you posts.</li>
    <li style="padding:0 1em; margin-left:1em;"><b>New tag clouds:</b> Display your tags in a table or tags from multiple groups combined into one tag cloud.</li>
    <li style="padding:0 1em; margin-left:1em;"><b>Tag cloud search:</b> Let your visitors filter tags in our tag clouds by parts of their names.</li>
  </ul>
  <p><?php printf( 'See the complete <a %1$s>feature comparison</a> or check out the <a %2$s>demos</a>.', 'href="https://chattymango.com/tag-groups-base-premium-comparison/?pk_campaign=tg&pk_kwd=dashboard" target="_blank"', 'href="https://demo.chattymango.com/tag-groups-premium-demo-page/?pk_campaign=tg&pk_kwd=dashboard" target="_blank"' ) ?></p>
  <?php if ( ! $tag_groups_premium_fs_sdk->is_paying() ) : ?>
    <div class="tg_premium_backend_call_to_action">
      <span style="float:right; margin: 0 10px;"><a href="<?php echo admin_url( 'admin.php?page=tag-groups-settings-pricing&trial=true' ) ?>" class="tg_premium_backend_call_to_action_button">Try Premium</a></span>
      <h3>
        Start your 7-day free trial!<br/>
        All features. Cancel anytime.
      </h3>
    </div>
  <?php endif; ?>
</div>
<div class="tg_premium_backend_right_image_box">
  <img src="<?php echo TAG_GROUPS_PLUGIN_URL ?>/assets/images/logo-chatty-mango-200x200.png" alt="Chatty Mango Logo" class=""/>
</div>

<div class="tg_premium_backend_right_image_box">
  <img src="<?php echo TAG_GROUPS_PLUGIN_URL ?>/assets/images/tgp-meta-box.png" alt="Tag Groups Meta Box" title="Replace the default tag meta box with one that understands your tag groups!" class="tg_premium_backend_right_image"/>
  <div class="tg_premium_backend_right_image_box_caption">Replace the default tag meta box with one that understands your tag groups!</div>
</div>

<div class="tg_premium_backend_bottom_image_box">
  <a href="https://demo.chattymango.com/toggle-post-filter-demos/?pk_campaign=tg&pk_kwd=dashboard" target="_blank">
    <img src="<?php echo TAG_GROUPS_PLUGIN_URL ?>/assets/images/tgp-dpf-toggles.png" class="tg_premium_backend_bottom_image" />
  </a>
  <div class="tg_premium_backend_bottom_image_box_caption">Toggle Post Filter: Visitors can search your posts by group and tags.</div>
</div>

<div class="tg_premium_backend_bottom_image_box">
  <a href="https://demo.chattymango.com/tag-groups-premium-demo-page/?pk_campaign=tg&pk_kwd=dashboard" target="_blank">
    <img src="<?php echo TAG_GROUPS_PLUGIN_URL ?>/assets/images/tag-groups-premium-shuffle-box-animated-800.gif" class="tg_premium_backend_bottom_image" />
  </a>
  <div class="tg_premium_backend_bottom_image_box_caption">Shuffle Box: Display a tag cloud that can filter tags by tag name and by group.</div>
</div>
