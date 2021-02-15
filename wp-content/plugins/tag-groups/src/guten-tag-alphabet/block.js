/**
 * BLOCK: tag-groups-alphabet-tabs
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
      this.helpComponent =
        'alphabetical-tag-cloud/alphabetical-tag-cloud-gutenberg-block/';
    } else {
      this.helpProduct = 'tag-groups';
      this.helpComponent =
        'alphabetical-tag-cloud-tag-clouds-and-groups-info/alphabetical-tag-cloud-gutenberg-block-2/';
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

class tagGroupsAlphabeticalCloudParameters extends Component {
  // Method for setting the initial state.
  static getInitialState(attributes) {
    let selectedGroups = []; // empty means all
    let selectedTaxonomies = ['post_tag'];
    let uniqueId =
      'tag_groups_render_' + Math.random().toString(36).substring(7);

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
      selectedTaxonomies: selectedTaxonomies, // array representation
      selectedGroups: selectedGroups, // array representation
      uniqueId: uniqueId,
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
    this.toggleOptionActive = this.toggleOptionActive.bind(this);
    this.toggleOptionCollapsible = this.toggleOptionCollapsible.bind(this);
    this.toggleOptionMouseover = this.toggleOptionMouseover.bind(this);
    this.toggleOptionAdjustSeparatorSize = this.toggleOptionAdjustSeparatorSize.bind(
      this
    );
    this.toggleOptionShowTagCount = this.toggleOptionShowTagCount.bind(this);
    this.toggleOptionDelay = this.toggleOptionDelay.bind(this);

    this.renderTabs = this.renderTabs.bind(this);

    // Load data from REST API.
    this.getGroupsFromApi();
    this.getTaxonomiesFromApi();
    this.getPostsFromApi();
  }

  handleChangeInclude(options) {
    let selectedGroups = options.map(function (option) {
      if (!isNaN(option.value)) {
        return option.value;
      }
    });

    // Set the state
    this.setState({ selectedGroups: selectedGroups });

    // Set the attributes
    this.props.setAttributes({
      include: selectedGroups.join(','),
    });
  }

  handleChangeTaxonomy(options) {
    let selectedTaxonomies = options.map(function (option) {
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

  toggleOptionActive() {
    let active = this.props.attributes.active < 0 ? 0 : -1; // -1 is a replacement for false, since data type is integer and 0 is reserved
    this.props.setAttributes({ active });
  }

  toggleOptionCollapsible() {
    let collapsible = this.props.attributes.collapsible ? 0 : 1;
    this.props.setAttributes({ collapsible });
  }

  toggleOptionMouseover() {
    let mouseover = this.props.attributes.mouseover ? 0 : 1;
    this.props.setAttributes({ mouseover });
  }

  toggleOptionAdjustSeparatorSize() {
    let adjust_separator_size =
      1 === this.props.attributes.adjust_separator_size ? 0 : 1;
    this.props.setAttributes({ adjust_separator_size });
  }

  toggleOptionShowTagCount() {
    let show_tag_count = this.props.attributes.show_tag_count ? 0 : 1;
    this.props.setAttributes({ show_tag_count });
  }

  toggleOptionDelay() {
    let delay = this.props.attributes.delay ? 0 : 1;
    this.props.setAttributes({ delay });
  }

  renderTabs() {
    const { active, collapsible, mouseover } = this.props.attributes;

    let options = {
      active: active < 0 ? false : active,
      collapsible: collapsible == 1,
    };

    if (mouseover) {
      options.event = 'mouseover';
    }

    setTimeout(() => {
      jQuery('#' + this.state.uniqueId).tabs(options);
    }, 1000);
  }

  render() {
    const { attributes, setAttributes } = this.props;

    const {
      active,
      adjust_separator_size,
      amount,
      append,
      assigned_class,
      collapsible,
      cover,
      custom_title,
      delay,
      div_class,
      div_id,
      exclude_letters,
      include_letters,
      largest,
      link_append,
      link_target,
      mouseover,
      order,
      orderby,
      prepend,
      separator,
      separator_size,
      show_tag_count,
      smallest,
      tags_post_id,
      threshold,
      ul_class,
    } = attributes;

    let optionsGroups = [],
      optionsTaxonomies = [],
      optionsActiveGroups = [];

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
    renderAttributes.delay = 0;
    delete renderAttributes.cover;

    let numberOfTabs = this.state.selectedGroups.length
      ? this.state.selectedGroups.length
      : this.state.groups.length - 1; // unassigned group

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
            <div>
              <TagGroupsHelp topic='separator' />
              <label htmlFor='tg_input_separator'>{__('Separator')}</label>
            </div>
            <PlainText
              id='tg_input_separator'
              className='input-control'
              value={separator ? separator : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(separator) => setAttributes({ separator })}
            />
            {separator && (
              <div>
                <TagGroupsHelp topic='adjust_separator_size' />
                <ToggleControl
                  label={__('Adjust separator size to following tag')}
                  checked={adjust_separator_size}
                  onChange={this.toggleOptionAdjustSeparatorSize}
                />
                {!adjust_separator_size && (
                  <div>
                    <TagGroupsHelp topic='separator_size' />
                    <RangeControl
                      label={__('Separator size')}
                      value={
                        separator_size !== undefined
                          ? Number(separator_size)
                          : 22
                      }
                      onChange={(separator_size) =>
                        setAttributes({ separator_size })
                      }
                      min={6}
                      max={144}
                    />
                  </div>
                )}
              </div>
            )}
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

          <PanelBody title={__('Alphabet and Tabs')} initialOpen={false}>
            <TagGroupsHelp topic='delay' />
            <ToggleControl
              label={__(
                'Delay the display of the tabs until they are fully rendered'
              )}
              checked={delay}
              onChange={this.toggleOptionDelay}
            />
            <div>
              <TagGroupsHelp topic='include_letters' />
              <label htmlFor='tg_input_include_letters'>
                {'Include letters'}
              </label>
            </div>
            <PlainText
              id='tg_input_include_letters'
              className='input-control'
              value={include_letters ? include_letters : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(include_letters) => setAttributes({ include_letters })}
            />
            <div>
              <TagGroupsHelp topic='exclude_letters' />
              <label htmlFor='tg_input_exclude_letters'>
                {'Exclude letters'}
              </label>
            </div>
            <PlainText
              id='tg_input_exclude_letters'
              className='input-control'
              value={exclude_letters ? exclude_letters : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(exclude_letters) => setAttributes({ exclude_letters })}
            />
            <TagGroupsHelp topic='mouseover' />
            <ToggleControl
              label={__('Open tabs on mouseover')}
              checked={mouseover}
              onChange={this.toggleOptionMouseover}
            />
            <TagGroupsHelp topic='collapsible' />
            <ToggleControl
              label={__('Make panels collapsible')}
              checked={collapsible}
              onChange={this.toggleOptionCollapsible}
            />
            {!!collapsible && numberOfTabs && (
              <div>
                <TagGroupsHelp topic='active' />
                <ToggleControl
                  label={__('Start with expanded tabs')}
                  checked={active >= 0}
                  onChange={this.toggleOptionActive}
                />
              </div>
            )}
            {(active >= 0 || !collapsible) && (
              <div>
                <TagGroupsHelp topic='select_active' />
                <label htmlFor='tg_input_active'>
                  {__('Which tab should be initially open?')}
                </label>
                <PlainText
                  id='tg_input_active'
                  className='input-control'
                  value={active >= 0 ? active + 1 : 1}
                  placeholder={__('Write a number')}
                  onChange={(active) => {
                    active = parseInt(active);
                    if (isNaN(active)) {
                      active = 1;
                    }
                    active--;
                    if (active < 0) {
                      active = 0;
                    }
                    setAttributes({ active });
                  }}
                />
              </div>
            )}
          </PanelBody>

          <PanelBody title={__('Groups')} initialOpen={false}>
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
          </PanelBody>

          <PanelBody title={__('Advanced Styling')} initialOpen={false}>
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
              <TagGroupsHelp topic='ul_class' />
              <label htmlFor='tg_input_ul_class'>{'ul class'}</label>
            </div>
            <PlainText
              id='tg_input_ul_class'
              className='input-control'
              value={ul_class ? ul_class : ''}
              placeholder={__('Write here or leave empty.')}
              onChange={(ul_class) => setAttributes({ ul_class })}
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
            block='chatty-mango/tag-groups-alphabet-tabs'
            className='chatty-mango-not-active'
            attributes={renderAttributes}
            onFetched={this.renderTabs}
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
              <h3 className='chatty-mango-editor-title'>
                {__('Alphabetical Tag Cloud')}
              </h3>
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
var cmTagGroupsAlphabetBlock = registerBlockType(
  'chatty-mango/tag-groups-alphabet-tabs',
  {
    title: __('Alphabetical Tag Cloud'),
    icon: 'tagcloud', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
    category: 'chatty-mango',
    description: __('Show your tags under tabs sorted by first letters.'),
    keywords: [__('alphabet'), __('tag cloud'), 'Chatty Mango'],
    html: false,
    transforms: {
      to: [
        {
          type: 'block',
          blocks: ['core/shortcode'],
          transform: function (attributes) {
            let parameters = [];
            for (var attribute in attributes) {
              if (attributes.hasOwnProperty(attribute)) {
                if (
                  null !== attributes[attribute] &&
                  '' !== attributes[attribute] &&
                  'source' !== attribute &&
                  cmTagGroupsAlphabetBlock.attributes[attribute] &&
                  attributes[attribute] !==
                    cmTagGroupsAlphabetBlock.attributes[attribute].default
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

            let text =
              '[tag_groups_alphabet_tabs ' + parameters.join(' ') + ']';
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
        cover: 'alphabetical-tag-cloud.png',
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
        // internal indicator to identify Gutebergb blocks
        type: 'string',
        default: '',
      },
      active: {
        // configurable in block
        type: 'integer',
        default: -1,
      },
      adjust_separator_size: {
        // configurable in block
        type: 'integer',
        default: 1,
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
      collapsible: {
        // configurable in block
        type: 'integer',
        default: 0,
      },
      custom_title: {
        // configurable in block
        type: 'string',
        default: '{description} ({count})',
      },
      delay: {
        // configurable in block
        type: 'integer',
        default: 1,
      },
      div_class: {
        // configurable in block
        type: 'string',
        default: 'tag-groups-cloud',
      },
      div_id: {
        // configurable in block
        type: 'string',
        default: '',
      },
      exclude_letters: {
        // only in shortcode
        type: 'string',
        default: '',
      },
      exclude_terms: {
        // only in shortcode
        type: 'string',
        default: '',
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
      include_letters: {
        // only in shortcode
        type: 'string',
        default: '',
      },
      include_terms: {
        // only in shortcode
        type: 'string',
        default: '',
      },
      largest: {
        // configurable in block
        type: 'integer',
        default: 22,
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
      mouseover: {
        // configurable in block
        type: 'integer',
        default: 0,
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
      separator_size: {
        // configurable in block
        type: 'integer',
        default: 22,
      },
      separator: {
        // configurable in block
        type: 'string',
        default: '',
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
      ul_class: {
        // configurable in block
        type: 'string',
        default: '',
      },
    },

    /**
     * Composing and rendering the editor content and control elements
     */
    edit: tagGroupsAlphabeticalCloudParameters,

    /**
     * We don't render any HTML when saving
     */
    save: function (props) {
      return null;
    },
  }
);
