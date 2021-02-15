/**
 * BLOCK: tag-groups-tag-list
 *
 *
 * @package     Tag Groups
 * @author      Christoph Amthor
 * @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license     GPL-3.0+
 * @since       0.38
 */

//	Import CSS.
// import './style.scss';
import '../editor.css';

import Select from 'react-select';
import apiFetch from '@wordpress/api-fetch';
import HeadingToolbar from '../heading-toolbar';
import TagGroupsServerSideRender from '../tag-groups-render';

const { __ } = wp.i18n;

const { createBlock, registerBlockType } = wp.blocks;

const { InspectorControls, PlainText } = wp.editor;

const { PanelBody, ToggleControl, RangeControl } = wp.components;

const { Component, Fragment } = wp.element;

const {
  siteUrl,
  siteLang,
  pluginUrl,
  hasPremium,
  serverSideRender,
  gutenbergSettings,
} = ChattyMangoTagGroupsGlobal;

const helpUrl = 'https://documentation.chattymango.com/documentation/';
const logoUrl = pluginUrl + '/assets/images/cm-tg-icon-64x64.png';

class TagGroupsHelp extends Component {
  constructor() {
    super();
    if (hasPremium) {
      this.helpProduct = 'tag-groups-premium';
      this.helpComponent = 'tag-list/tag-list-gutenberg-block/';
    } else {
      this.helpProduct = 'tag-groups';
      this.helpComponent =
        'tag-list-tag-clouds-and-groups-info/tag-list-gutenberg-block-2/';
    }
  }

  render() {
    let href;

    if (this.props.topic === 'transform-your-block-for-more-options') {
      href =
        helpUrl +
        this.helpProduct +
        '/tag-clouds-and-groups-info/using-gutenberg/#transforming-blocks-to-shortcodes';
    } else {
      href = helpUrl + this.helpProduct + '/' + this.helpComponent;

      if ('' != siteLang) {
        href += '?lang=' + siteLang;
      }

      href += '#' + this.props.topic;
    }

    let tooltip = __('Click for help!');

    return (
      <div>
        <a
          href={href}
          target='_blank'
          style={{ textDecoration: 'none' }}
          title={tooltip}
        >
          <span className='dashicons dashicons-editor-help tg_right chatty-mango-help-icon'></span>
        </a>
      </div>
    );
  }
}

class editFunction extends Component {
  // Method for setting the initial state.
  static getInitialState(attributes) {
    let selectedGroups = []; // empty means all
    let selectedTaxonomies = ['post_tag'];
    let uniqueId =
      'tag_groups_render_' +
      Math.random()
        .toString(36)
        .substring(7);

    // We need arrays for the select elements.
    if (attributes.include) {
      selectedGroups = attributes.include.split(',').map((x) => {
        return parseInt(x, 10);
      });
    }

    if (attributes.taxonomy) {
      selectedTaxonomies = attributes.taxonomy.split(',');
    }

    return {
      groups: [],
      taxonomies: [],
      posts: [],
      selectedGroups, // array representation
      selectedTaxonomies, // array representation
      uniqueId,
    };
  }

  // Constructing our component. With super() we are setting everything to 'this'.
  // Now we can access the attributes with this.props.attributes
  constructor() {
    super(...arguments);

    const { attributes, setAttributes } = this.props;

    this.groupsEndPoint = '/tag-groups/v1/groups';
    this.termsEndPoint = '/tag-groups/v1/terms';
    this.taxonomiesEndPoint = '/tag-groups/v1/taxonomies';

    this.state = this.constructor.getInitialState(attributes);

    if (!attributes.hide_empty) {
      setAttributes({ threshold: 0 });
    }

    if (attributes.threshold) {
      setAttributes({ hide_empty: 1 });
    } else {
      setAttributes({ hide_empty: 0 });
    }

    // Bind so we can use 'this' inside the method.
    this.getGroupsFromApi = this.getGroupsFromApi.bind(this);
    this.getTaxonomiesFromApi = this.getTaxonomiesFromApi.bind(this);
    this.getPostsFromApi = this.getPostsFromApi.bind(this);
    this.handleChangeInclude = this.handleChangeInclude.bind(this);
    this.handleChangeTaxonomy = this.handleChangeTaxonomy.bind(this);
    this.toggleOptionHideEmptyContent = this.toggleOptionHideEmptyContent.bind(
      this
    );
    this.toggleOptionKeepTogether = this.toggleOptionKeepTogether.bind(this);
    this.toggleOptionAddPremiumFilter = this.toggleOptionAddPremiumFilter.bind(
      this
    );
    this.toggleOptionShowTagCount = this.toggleOptionShowTagCount.bind(this);

    // Load data from REST API.
    this.getGroupsFromApi();
    this.getTaxonomiesFromApi();
    this.getPostsFromApi();
  }

  handleChangeInclude(options) {
    let selectedGroups = options.map(function(option) {
      if (!isNaN(option.value)) {
        return option.value;
      }
    });

    // Set the state
    this.setState({ selectedGroups });

    // Set the attributes
    this.props.setAttributes({
      include: selectedGroups.join(','),
    });

    if (selectedGroups.indexOf(0) > -1) {
      this.props.setAttributes({
        show_not_assigned: 1,
      });
    } else {
      this.props.setAttributes({
        show_not_assigned: 0,
      });
    }
  }

  handleChangeTaxonomy(options) {
    let selectedTaxonomies = options.map(function(option) {
      if (typeof option.value === 'string') {
        return option.value;
      }
    });

    // Set the state
    this.setState({ selectedTaxonomies });

    // Set the attributes
    this.props.setAttributes({
      taxonomy: selectedTaxonomies.join(','),
    });
  }

  /**
   * Loading Groups
   */
  getGroupsFromApi() {
    // retrieve the groups
    apiFetch({ path: this.groupsEndPoint })
      .then((groups) => {
        if (groups) {
          this.setState({ groups });
        }
      })
      .catch((error) => {
        if (this.isStillMounted && fetchRequest === this.currentFetchRequest) {
          this.setState({
            response: {
              error: true,
              errorMsg: error.message,
            },
          });
        }
      });
  }

  /**
   * Loading Taxonomies (own REST API endpoint)
   */
  getTaxonomiesFromApi() {
    // retrieve the taxonomies
    apiFetch({ path: this.taxonomiesEndPoint })
      .then((taxonomies) => {
        if (taxonomies) {
          this.setState({ taxonomies });
        }
      })
      .catch((error) => {
        if (this.isStillMounted && fetchRequest === this.currentFetchRequest) {
          this.setState({
            response: {
              error: true,
              errorMsg: error.message,
            },
          });
        }
      });
  }

  /**
   * Loading Posts
   */
  getPostsFromApi() {
    apiFetch({ path: '/wp/v2/posts?per_page=100' })
      .then((fullPosts) => {
        if (fullPosts) {
          let posts = [
            { value: -1, label: __('off') },
            { value: 0, label: __('[use this post]') },
          ];
          fullPosts.forEach((fullPost) => {
            posts.push({
              value: fullPost.id,
              label: fullPost.title.rendered,
            });
          });
          this.setState({ posts });
        }
      })
      .catch((error) => {
        if (this.isStillMounted && fetchRequest === this.currentFetchRequest) {
          this.setState({
            response: {
              error: true,
              errorMsg: error.message,
            },
          });
        }
      });
  }

  toggleOptionHideEmptyContent() {
    let hide_empty_content =
      1 === this.props.attributes.hide_empty_content ? 0 : 1;
    this.props.setAttributes({ hide_empty_content });
  }

  toggleOptionKeepTogether() {
    let keep_together = this.props.attributes.keep_together ? 0 : 1;
    this.props.setAttributes({ keep_together });
  }

  toggleOptionAddPremiumFilter(key) {
    let add_premium_filter =
      1 === this.props.attributes.add_premium_filter ? 0 : 1;
    this.props.setAttributes({ add_premium_filter });
  }

  toggleOptionShowTagCount() {
    let show_tag_count = this.props.attributes.show_tag_count ? 0 : 1;
    this.props.setAttributes({ show_tag_count });
  }

  render() {
    const { attributes, setAttributes } = this.props;

    const {
      add_premium_filter,
      amount,
      append,
      assigned_class,
      column_count,
      column_gap,
      cover,
      custom_title,
      div_class,
      div_id,
      groups_post_id,
      h_level,
      header_class,
      hide_empty_content,
      include,
      keep_together,
      largest,
      link_append,
      link_target,
      not_assigned_name,
      order,
      orderby,
      prepend,
      show_not_assigned,
      show_tag_count,
      smallest,
      tags_div_class,
      tags_post_id,
      taxonomy,
      threshold,
    } = attributes;

    let optionsGroups = [],
      optionsTaxonomies = [];

    if (this.state.groups && this.state.groups.length > 0) {
      this.state.groups.forEach((group) => {
        optionsGroups.push({ value: group.term_group, label: group.label });
      });
    }

    if (this.state.taxonomies && this.state.taxonomies.length > 0) {
      this.state.taxonomies.forEach((taxonomy) => {
        optionsTaxonomies.push({ value: taxonomy.slug, label: taxonomy.name });
      });
    }

    if (attributes.source !== 'gutenberg') {
      setAttributes({ source: 'gutenberg' });
    }

    let renderAttributes = { ...attributes };
    renderAttributes.div_id = this.state.uniqueId;
    delete renderAttributes.cover;

    return [
      <InspectorControls key='inspector'>
        <div className='chatty-mango-inspector-control'>
          <PanelBody title={__('Tags and Terms')} initialOpen={false}>
            <TagGroupsHelp topic='taxonomy' />
            <label htmlFor='tg_input_taxonomy'>
              {__('Include taxonomies')}
            </label>
            <Select
              id='tg_input_taxonomy'
              onChange={this.handleChangeTaxonomy}
              value={this.state.selectedTaxonomies}
              options={optionsTaxonomies}
              multi={true}
              closeOnSelect={false}
              removeSelected={true}
            />
            <TagGroupsHelp topic='smallest' />
            <RangeControl
              label={__('Smallest font size')}
              value={smallest !== undefined ? Number(smallest) : 12}
              onChange={(value) => {
                if (value <= largest && value < 73)
                  setAttributes({ smallest: value });
              }}
              min={6}
              max={72}
            />
            <TagGroupsHelp topic='largest' />
            <RangeControl
              label={__('Largest font size')}
              value={largest !== undefined ? Number(largest) : 22}
              onChange={(value) => {
                if (smallest <= value && value > 5)
                  setAttributes({ largest: value });
              }}
              min={6}
              max={72}
            />
            <TagGroupsHelp topic='amount' />
            <RangeControl
              label={
                __('Tags per group') +
                (amount == 0 ? ': ' + __('unlimited') : '')
              }
              value={amount !== undefined ? Number(amount) : 0}
              onChange={(amount) => setAttributes({ amount })}
              min={0}
              max={200}
            />
            <TagGroupsHelp topic='orderby' />
            <label htmlFor='tg_input_orderby'>{__('Order tags by')}</label>
            <Select
              id='tg_input_orderby'
              onChange={(option) => {
                if (option) setAttributes({ orderby: option.value });
              }}
              value={orderby && typeof orderby === 'string' ? orderby : 'name'}
              options={[
                { value: 'name', label: __('Name') },
                { value: 'natural', label: __('Natural sorting') },
                { value: 'count', label: __('Post count') },
                { value: 'slug', label: __('Slug') },
                { value: 'term_id', label: __('Term ID') },
                { value: 'description', label: __('Description') },
                { value: 'term_order', label: __('Term Order') },
              ]}
            />
            <TagGroupsHelp topic='order' />
            <label htmlFor='tg_input_order'>{__('Sort order')}</label>
            <Select
              id='tg_input_order'
              onChange={(option) => {
                if (option) setAttributes({ order: option.value });
              }}
              value={
                order && typeof order === 'string' ? order.toUpperCase() : 'ASC'
              }
              options={[
                { value: 'ASC', label: __('Ascending') },
                { value: 'DESC', label: __('Descending') },
              ]}
            />
            <TagGroupsHelp topic='threshold' />
            <RangeControl
              label={__('Minimum post count for tags to appear')}
              value={threshold !== undefined ? Number(threshold) : 0}
              onChange={(threshold) => {
                setAttributes({ threshold });
                if (0 === threshold) {
                  setAttributes({ hide_empty: 0 });
                } else {
                  setAttributes({ hide_empty: 1 });
                }
              }}
              min={0}
              max={50}
            />
            <TagGroupsHelp topic='prepend' />
            <div>
              <label htmlFor='tg_input_prepend'>{__('Prepend')}</label>
            </div>
            <PlainText
              id='tg_input_prepend'
              className='input-control'
              value={prepend ? prepend : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(prepend) => setAttributes({ prepend })}
            />
            <TagGroupsHelp topic='append' />
            <div>
              <label htmlFor='tg_input_append'>{__('Append')}</label>
            </div>
            <PlainText
              id='tg_input_append'
              className='input-control'
              value={append ? append : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(append) => setAttributes({ append })}
            />
            {!custom_title && (
              <div>
                <TagGroupsHelp topic='show_tag_count' />
                <ToggleControl
                  label={__('Show post count in the tooltip')}
                  checked={show_tag_count}
                  onChange={this.toggleOptionShowTagCount}
                />
              </div>
            )}
            <div>
              <TagGroupsHelp topic='custom_title' />
              <label htmlFor='tg_input_custom_title'>
                {__('Custom title')}
              </label>
            </div>
            <PlainText
              id='tg_input_custom_title'
              className='input-control'
              value={custom_title ? custom_title : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(custom_title) => setAttributes({ custom_title })}
            />
            <TagGroupsHelp topic='link_target' />
            <label htmlFor='tg_input_link_target'>{__('Link target')}</label>
            <Select
              id='tg_input_link_target'
              onChange={(option) => {
                if (option) setAttributes({ link_target: option.value });
              }}
              value={
                link_target && typeof link_target === 'string'
                  ? link_target
                  : '_self'
              }
              options={[
                { value: '_self', label: '_self' },
                { value: '_blank', label: '_blank' },
                { value: '_parent', label: '_parent' },
                { value: '_top', label: '_top' },
              ]}
            />
            <div>
              <label htmlFor='tg_input_link_append'>
                {__('Append to link')}
              </label>
            </div>
            <PlainText
              id='tg_input_link_append'
              className='input-control'
              value={link_append ? link_append : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(link_append) => setAttributes({ link_append })}
            />
            {hasPremium && (
              <div>
                <TagGroupsHelp topic='add_premium_filter' />
                <ToggleControl
                  label={__('Add filter to tags for multiple groups.')}
                  checked={add_premium_filter}
                  onChange={this.toggleOptionAddPremiumFilter}
                />
              </div>
            )}
            <TagGroupsHelp topic='tags_post_id' />
            <label htmlFor='tg_input_tags_post_id'>
              {__('Use tags of the following post:')}
            </label>
            <Select
              id='tg_input_tags_post_id'
              onChange={(option) => {
                if (option) setAttributes({ tags_post_id: option.value });
              }}
              value={tags_post_id}
              options={this.state.posts}
            />
          </PanelBody>

          <PanelBody title={__('Groups')} initialOpen={false}>
            <TagGroupsHelp topic='hide_empty_content' />
            <ToggleControl
              label={__('Hide empty groups')}
              checked={hide_empty_content}
              onChange={this.toggleOptionHideEmptyContent}
            />
            <TagGroupsHelp topic='include' />
            <label htmlFor='tg_input_include'>{__('Include groups')}</label>
            <Select
              id='tg_input_include'
              onChange={this.handleChangeInclude}
              value={this.state.selectedGroups}
              options={optionsGroups}
              multi={true}
              closeOnSelect={false}
              removeSelected={true}
            />
            {show_not_assigned === 1 && (
              <div>
                <div>
                  <label htmlFor='tg_input_not_assigned_name'>
                    {__('Label on tab for not-assigned tags')}
                  </label>
                </div>
                <PlainText
                  id='tg_input_not_assigned_name'
                  className='input-control'
                  value={not_assigned_name ? not_assigned_name : 'not assigned'}
                  placeholder={__('Write here or leave empty.')}
                  onChange={(not_assigned_name) =>
                    setAttributes({ not_assigned_name })
                  }
                />
              </div>
            )}
            <TagGroupsHelp topic='groups_post_id' />
            <label htmlFor='tg_input_group_post_id'>
              {__('Use groups of the following post:')}
            </label>
            <Select
              id='tg_input_group_post_id'
              onChange={(option) => {
                if (option) setAttributes({ groups_post_id: option.value });
              }}
              value={groups_post_id}
              options={this.state.posts}
            />
          </PanelBody>

          <PanelBody title={__('Columns')} initialOpen={false}>
            <TagGroupsHelp topic='column_count' />
            <RangeControl
              label={__('Column count')}
              value={column_count >= 0 ? Number(column_count) : 2}
              onChange={(value) => setAttributes({ column_count: value })}
              min={0}
              max={10}
            />
            {column_count > 1 && (
              <div>
                <div>
                  <TagGroupsHelp topic='column_gap' />
                  <label htmlFor='tg_input_column_gap'>
                    {'Gap between columns'}
                  </label>
                </div>
                <PlainText
                  id='tg_input_column_gap'
                  className='input-control'
                  value={column_gap ? column_gap : '10px'}
                  placeholder={__('Write here or leave empty.')}
                  onChange={(column_gap) => setAttributes({ column_gap })}
                />
                <TagGroupsHelp topic='keep_together' />
                <ToggleControl
                  label={__('Avoid breaking groups across columns')}
                  checked={keep_together}
                  onChange={this.toggleOptionKeepTogether}
                />
              </div>
            )}
          </PanelBody>

          <PanelBody title={__('Advanced Styling')} initialOpen={false}>
            <div>
              <TagGroupsHelp topic='h_level' />
              <label htmlFor='tg_input_h_level'>{'Title element'}</label>
            </div>
            <HeadingToolbar
              id='tg_input_h_level'
              isCollapsed={false}
              minLevel={1}
              maxLevel={7}
              selectedLevel={h_level}
              onChange={(newLevel) => setAttributes({ h_level: newLevel })}
            />
            <div>
              <TagGroupsHelp topic='div_id' />
              <label htmlFor='tg_input_div_id'>{'div id'}</label>
            </div>
            <PlainText
              id='tg_input_div_id'
              className='input-control'
              value={div_id ? div_id : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(div_id) => setAttributes({ div_id })}
            />
            <div>
              <TagGroupsHelp topic='div_class' />
              <label htmlFor='tg_input_div_class'>{'div class'}</label>
            </div>
            <PlainText
              id='tg_input_div_class'
              className='input-control'
              value={div_class ? div_class : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(div_class) => setAttributes({ div_class })}
            />
            <div>
              <TagGroupsHelp topic='tags_div_class' />
              <label htmlFor='tg_input_tags_div_class'>
                {'tags div class'}
              </label>
            </div>
            <PlainText
              id='tg_input_tags_div_class'
              className='input-control'
              value={tags_div_class ? tags_div_class : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(tags_div_class) => setAttributes({ tags_div_class })}
            />
            <div>
              <TagGroupsHelp topic='header_class' />
              <label htmlFor='tg_input_header_class'>{'header class'}</label>
            </div>
            <PlainText
              id='tg_input_header_class'
              className='input-control'
              value={header_class ? header_class : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(header_class) => setAttributes({ header_class })}
            />
            {tags_post_id !== -1 && (
              <div>
                <div>
                  <TagGroupsHelp topic='assigned_class' />
                  <label htmlFor='tg_input_assigned_class'>
                    {'<a class="..._0"> or <a class="..._1">'}
                  </label>
                </div>
                <PlainText
                  id='tg_input_assigned_class'
                  className='input-control'
                  value={assigned_class ? assigned_class : ''}
                  placeholder={__('Write here or leave empty.')}
                  onChange={(assigned_class) =>
                    setAttributes({ assigned_class })
                  }
                />
              </div>
            )}
          </PanelBody>
          <div className='chatty-mango-help-transform'>
            <TagGroupsHelp topic='transform-your-block-for-more-options' />
            <div
              className='dashicons-before dashicons-editor-code'
              dangerouslySetInnerHTML={{
                __html: __(
                  'If you want to customize further options, you need to transform the block into a <b>shortcode block</b>.'
                ),
              }}
            ></div>
          </div>
          <div
            className='chatty-mango-inspector-help dashicons-before dashicons-admin-generic'
            dangerouslySetInnerHTML={{
              __html: __(
                `The live preview of blocks can be turned on and off in the Tag Groups Settings under <a href="${gutenbergSettings}">Back End → Gutenberg</a>.`,
                'tag-groups'
              ),
            }}
          ></div>
        </div>
      </InspectorControls>,
      <div>
        {!!cover && (
          <Fragment>
            <img src={pluginUrl + '/assets/images/features/' + cover} />
          </Fragment>
        )}
        {!cover && serverSideRender && (
          <TagGroupsServerSideRender
            block='chatty-mango/tag-groups-tag-list'
            className='chatty-mango-not-active'
            attributes={renderAttributes}
          />
        )}
        {!cover && !serverSideRender && (
          <div className='chatty-mango-editor'>
            <div className='chatty-mango-editor-block'>
              <img
                src={logoUrl}
                alt='logo'
                style={{ float: 'left', margin: 15 }}
              />
            </div>
            <div className='chatty-mango-editor-block'>
              <h3 className='chatty-mango-editor-title'>{__('Tag List')}</h3>
              <div className='cm-gutenberg dashicons-before dashicons-admin-generic'>
                {__(
                  'Select this block and customize the tag cloud in the Inspector.'
                )}
              </div>
              <div className='cm-gutenberg dashicons-before dashicons-welcome-view-site'>
                {__('See the output with Preview.')}
              </div>
            </div>
          </div>
        )}
      </div>,
    ];
  }
}

/**
 * Register: a Gutenberg Block.
 *
 * @param  {string}	  name	   Block name.
 * @param  {Object}	  settings Block settings.
 * @return {?WPBlock}		   The block, if it has been successfully
 *							   registered; otherwise `undefined`.
 */
var cmTagGroupsTabsBlock = registerBlockType(
  'chatty-mango/tag-groups-tag-list',
  {
    title: __('Tag List'),
    icon: 'tag', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
    category: 'chatty-mango',
    description: __('Show your tags listed under groups.'),
    keywords: [__('tabs'), __('tag cloud'), 'Chatty Mango'],
    html: false,
    transforms: {
      to: [
        {
          type: 'block',
          blocks: ['core/shortcode'],
          transform: function(attributes) {
            let parameters = [];
            for (var attribute in attributes) {
              if (attributes.hasOwnProperty(attribute)) {
                if (
                  null !== attributes[attribute] &&
                  '' !== attributes[attribute] &&
                  'source' !== attribute &&
                  cmTagGroupsTabsBlock.attributes[attribute] &&
                  attributes[attribute] !==
                    cmTagGroupsTabsBlock.attributes[attribute].default
                ) {
                  if (typeof attributes[attribute] === 'number') {
                    parameters.push(attribute + '=' + attributes[attribute]);
                  } else {
                    if (attributes[attribute].indexOf('"') === -1) {
                      parameters.push(
                        attribute + '="' + attributes[attribute] + '"'
                      );
                    } else {
                      parameters.push(
                        attribute + "='" + attributes[attribute] + "'"
                      );
                    }
                  }
                }
              }
            }

            let text = '[tag_groups_tag_list ' + parameters.join(' ') + ']';
            return createBlock('core/shortcode', {
              text,
            });
          },
        },
      ],
    },
    supports: {
      html: false,
      customClassName: false,
    },
    example: {
      attributes: {
        cover: 'tag-list.png',
      },
    },

    /**
     * Attributes are the same as shortcode parameters
     **/
    attributes: {
      cover: {
        type: 'string',
        default: '',
      },
      source: {
        // internal indicator to identify Gutenberg blocks
        type: 'string',
        default: '',
      },
      add_premium_filter: {
        // configurable in block
        type: 'integer',
        default: 0,
      },
      amount: {
        // configurable in block
        type: 'integer',
        default: 0,
      },
      append: {
        // configurable in block
        type: 'string',
        default: '',
      },
      assigned_class: {
        // configurable in block
        type: 'string',
        default: '',
      },
      column_count: {
        type: 'integer',
        default: 2,
      },
      column_gap: {
        type: 'string',
        default: '10px',
      },
      custom_title: {
        // configurable in block
        type: 'string',
        default: '{description} ({count})',
      },
      div_class: {
        // configurable in block
        type: 'string',
        default: 'tag-groups-tag-list',
      },
      div_id: {
        // configurable in block
        type: 'string',
        default: '',
      },
      exclude_terms: {
        // only in shortcode
        type: 'string',
        default: '',
      },
      groups_post_id: {
        // configurable in block
        type: 'integer',
        default: -1,
      },
      h_level: {
        // configurable in block
        type: 'integer',
        default: 3,
      },
      header_class: {
        type: 'string',
        default: '',
      },
      hide_empty_content: {
        // configurable in block
        type: 'integer',
        default: 0,
      },
      hide_empty: {
        // configurable in block
        type: 'integer',
        default: 1,
      },
      include: {
        // configurable in block
        type: 'string',
        default: '',
      },
      include_terms: {
        // only in shortcode
        type: 'string',
        default: '',
      },
      keep_together: {
        // configurable in block
        type: 'integer',
        default: 1,
      },
      largest: {
        // configurable in block
        type: 'integer',
        default: 12,
      },
      link_append: {
        // configurable in block
        type: 'string',
        default: '',
      },
      link_target: {
        // configurable in block
        type: 'string',
        default: '_self',
      },
      not_assigned_name: {
        // configurable in block
        type: 'string',
        default: '',
      },
      order: {
        // configurable in block
        type: 'string',
        default: 'ASC',
      },
      orderby: {
        // configurable in block
        type: 'string',
        default: 'name',
      },
      prepend: {
        // configurable in block
        type: 'string',
        default: '',
      },
      show_not_assigned: {
        // indirectly configurable in block
        type: 'integer',
        default: 0,
      },
      show_all_groups: {
        // only in shortcode
        type: 'integer',
        default: 0,
      },
      show_tag_count: {
        // configurable in block
        type: 'integer',
        default: 1,
      },
      smallest: {
        // configurable in block
        type: 'integer',
        default: 12,
      },
      tags_div_class: {
        // configurable in block
        type: 'string',
        default: 'tag-groups-tag-list-tags',
      },
      tags_post_id: {
        // configurable in block
        type: 'integer',
        default: -1,
      },
      taxonomy: {
        // configurable in block
        type: 'string',
        default: '',
      },
      threshold: {
        // configurable in block
        type: 'integer',
        default: 0,
      },
    },

    /**
     * Composing and rendering the editor content and control elements
     */
    edit: editFunction,

    /**
     * We don't render any HTML when saving
     */
    save: (props) => null,
  }
);
