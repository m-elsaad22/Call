<?php
defined( 'ABSPATH' ) || exit;
/**
 *
 */
class YourColor__Schema{

	function __construct($argument=array()){

	}

	protected function print_jsonld( $schema ) {
		if ( empty( $schema ) || ! is_array( $schema ) ) {
			return;
		}
		echo '<script type="application/ld+json">';
		echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		echo '</script>';
	}

	# SINGLE EDITS .
		public function Single(){
			global $post;

            	$Permalink = get_the_permalink( $post->ID );
			    $publish_date = get_the_date('Y-m-d');
			    $modified_date = get_the_modified_date('Y-m-d');
			    $sitename__schema = get_option('sitename__schema');
			    $logo__schema = get_option('logo__schema');
			    $post_author = get_userdata($post->post_author);
			    # حماية: مقال بدون مؤلف صالح (مستخدم محذوف) — كائن بديل يمنع تحذير PHP (v1.2.1)
			    if ( ! $post_author ) {
			    	$post_author = (object) array( 'display_name' => get_bloginfo('name'), 'ID' => 0 );
			    }
			    $Author__url = get_author_posts_url($post_author->ID);
			    $thumbnail_url = get_the_post_thumbnail_url( $post->ID );


            	# ImageObject
            		$hide_schema_ImageObject = get_option('hide_schema_ImageObject');
            		if( empty( $hide_schema_ImageObject ) && !empty( $sitename__schema ) && !empty( $logo__schema )  ) {
						$YourColor_ImageObject = get_post_meta( $post->ID,'YourColor_ImageObject',true);
						$YourColor_ImageObject = ( is_array( $YourColor_ImageObject ) ) ? $YourColor_ImageObject : array();
						#
						if( !isset( $YourColor_ImageObject['hide_schema_ImageObject'] ) || ( isset( $YourColor_ImageObject['hide_schema_ImageObject'] ) && empty( $YourColor_ImageObject['hide_schema_ImageObject'] ) ) ) {

							$defualt_ImageObject = get_option('YourColor_ImageObject');
							$defualt_ImageObject = ( is_array( $defualt_ImageObject ) ) ? $defualt_ImageObject : array();
							if( !isset( $defualt_ImageObject['description'] ) || ( isset( $defualt_ImageObject['description'] ) && empty( $defualt_ImageObject['description'] ) ) ) $defualt_ImageObject['description'] = wp_trim_words( $post->post_content,20);
							if( !isset( $defualt_ImageObject['contentLocation'] ) || ( isset( $defualt_ImageObject['contentLocation'] ) && empty( $defualt_ImageObject['contentLocation'] ) ) ) $defualt_ImageObject['contentLocation'] = '';
							#
							if( !isset( $YourColor_ImageObject['description'] ) || ( isset( $YourColor_ImageObject['description'] ) && empty( $YourColor_ImageObject['description'] ) ) ) $YourColor_ImageObject['description'] = $defualt_ImageObject['description'];
							if( !isset( $YourColor_ImageObject['contentLocation'] ) || ( isset( $YourColor_ImageObject['contentLocation'] ) && empty( $YourColor_ImageObject['contentLocation'] ) ) ) $YourColor_ImageObject['contentLocation'] = $defualt_ImageObject['contentLocation'];

							$thumbnail_url = get_the_post_thumbnail_url( $post->ID );
							if( !empty( $thumbnail_url ) ){
					            $this->print_jsonld( array(
					                '@context' => 'http://schema.org',
					                '@type' => 'ImageObject',
					                'url' => $Permalink,
					                'datePublished' => $publish_date,
					                'dateModified' => $modified_date,
					                'description' => $YourColor_ImageObject['description'],
					                'uploadDate' => date('Y-m-d H:i:s', strtotime($publish_date) ),
					                'contentUrl' => $thumbnail_url,
					                'contentLocation' => $YourColor_ImageObject['contentLocation'],
					                'publisher' => array(
					                    '@type' => 'Organization',
					                    'name' => $sitename__schema,
					                    'logo' => array(
						                        '@type' => 'ImageObject',
						                        'url' => $logo__schema,
					                      	),
					                  	),
					                'author' => array(
					                    '@type' => 'Person',
					                    'name' => $post_author->display_name,
					                ),
					            ) );
							}
						}
            		}

	     	 	# YourColor_Service .	
	          		$hide_schema_Service = get_option('hide_schema_Service');
	        		if( empty( $hide_schema_Service ) ) {
						$YourColor_Service = get_post_meta( $post->ID,'YourColor_Service',true);
						$YourColor_Service = ( is_array( $YourColor_Service ) ) ? $YourColor_Service : array();
						#
						if( !isset( $YourColor_Service['hide_schema_Service'] ) || ( isset( $YourColor_Service['hide_schema_Service'] ) && empty( $YourColor_Service['hide_schema_Service'] ) ) ) {

							$defualt_Service = get_option('YourColor_Service');
							$defualt_Service = ( is_array( $defualt_Service ) ) ? $defualt_Service : array();
							if( !isset( $defualt_Service['priceRange'] ) || ( isset( $defualt_Service['priceRange'] ) && empty( $defualt_Service['priceRange'] ) ) ) $defualt_Service['priceRange'] = '$';
							if( !isset( $defualt_Service['description'] ) || ( isset( $defualt_Service['description'] ) && empty( $defualt_Service['description'] ) ) ) $defualt_Service['description'] = wp_trim_words( $post->post_content,20);
							
							if( !isset( $defualt_Service['addressLocality'] ) || ( isset( $defualt_Service['addressLocality'] ) && empty( $defualt_Service['addressLocality'] ) ) ) $defualt_Service['addressLocality'] = '';
							if( !isset( $defualt_Service['postalCode'] ) || ( isset( $defualt_Service['postalCode'] ) && empty( $defualt_Service['postalCode'] ) ) ) $defualt_Service['postalCode'] = '';
							if( !isset( $defualt_Service['telephone'] ) || ( isset( $defualt_Service['telephone'] ) && empty( $defualt_Service['telephone'] ) ) ) $defualt_Service['telephone'] = '';
							if( !isset( $defualt_Service['addressCountry'] ) || ( isset( $defualt_Service['addressCountry'] ) && empty( $defualt_Service['addressCountry'] ) ) ) $defualt_Service['addressCountry'] = '';
							if( !isset( $defualt_Service['streetAddress'] ) || ( isset( $defualt_Service['streetAddress'] ) && empty( $defualt_Service['streetAddress'] ) ) ) $defualt_Service['streetAddress'] = '';
							if( !isset( $defualt_Service['addressRegion'] ) || ( isset( $defualt_Service['addressRegion'] ) && empty( $defualt_Service['addressRegion'] ) ) ) $defualt_Service['addressRegion'] = '';
							if( !isset( $defualt_Service['areaServed'] ) || ( isset( $defualt_Service['areaServed'] ) && empty( $defualt_Service['areaServed'] ) ) ) $defualt_Service['areaServed'] = '';
							if( !isset( $defualt_Service['OfferCatalog'] ) || ( isset( $defualt_Service['OfferCatalog'] ) && empty( $defualt_Service['OfferCatalog'] ) ) ) $defualt_Service['OfferCatalog'] = '';
							if( !isset( $defualt_Service['identifier'] ) || ( isset( $defualt_Service['identifier'] ) && empty( $defualt_Service['identifier'] ) ) ) $defualt_Service['identifier'] = '';
							if( !isset( $defualt_Service['additionalType'] ) || ( isset( $defualt_Service['additionalType'] ) && empty( $defualt_Service['additionalType'] ) ) ) $defualt_Service['additionalType'] = '';
							if( !isset( $defualt_Service['ratingValue'] ) || ( isset( $defualt_Service['ratingValue'] ) && empty( $defualt_Service['ratingValue'] ) ) ) $defualt_Service['ratingValue'] = '';
							if( !isset( $defualt_Service['reviewCount'] ) || ( isset( $defualt_Service['reviewCount'] ) && empty( $defualt_Service['reviewCount'] ) ) ) $defualt_Service['reviewCount'] = '';

							#
							if( !isset( $YourColor_Service['priceRange'] ) || ( isset( $YourColor_Service['priceRange'] ) && empty( $YourColor_Service['priceRange'] ) ) ) $YourColor_Service['priceRange'] = $defualt_Service['priceRange'];
							if( !isset( $YourColor_Service['description'] ) || ( isset( $YourColor_Service['description'] ) && empty( $YourColor_Service['description'] ) ) ) $YourColor_Service['description'] = $defualt_Service['description'];

							if( !isset( $YourColor_Service['addressLocality'] ) || ( isset( $YourColor_Service['addressLocality'] ) && empty( $YourColor_Service['addressLocality'] ) ) ) $YourColor_Service['addressLocality'] = $defualt_Service['addressLocality'];
							if( !isset( $YourColor_Service['postalCode'] ) || ( isset( $YourColor_Service['postalCode'] ) && empty( $YourColor_Service['postalCode'] ) ) ) $YourColor_Service['postalCode'] = $defualt_Service['postalCode'];
							if( !isset( $YourColor_Service['telephone'] ) || ( isset( $YourColor_Service['telephone'] ) && empty( $YourColor_Service['telephone'] ) ) ) $YourColor_Service['telephone'] = $defualt_Service['telephone'];
							if( !isset( $YourColor_Service['addressCountry'] ) || ( isset( $YourColor_Service['addressCountry'] ) && empty( $YourColor_Service['addressCountry'] ) ) ) $YourColor_Service['addressCountry'] = $defualt_Service['addressCountry'];
							if( !isset( $YourColor_Service['streetAddress'] ) || ( isset( $YourColor_Service['streetAddress'] ) && empty( $YourColor_Service['streetAddress'] ) ) ) $YourColor_Service['streetAddress'] = $defualt_Service['streetAddress'];
							if( !isset( $YourColor_Service['addressRegion'] ) || ( isset( $YourColor_Service['addressRegion'] ) && empty( $YourColor_Service['addressRegion'] ) ) ) $YourColor_Service['addressRegion'] = $defualt_Service['addressRegion'];
							if( !isset( $YourColor_Service['areaServed'] ) || ( isset( $YourColor_Service['areaServed'] ) && empty( $YourColor_Service['areaServed'] ) ) ) $YourColor_Service['areaServed'] = $defualt_Service['areaServed'];
							if( !isset( $YourColor_Service['OfferCatalog'] ) || ( isset( $YourColor_Service['OfferCatalog'] ) && empty( $YourColor_Service['OfferCatalog'] ) ) ) $YourColor_Service['OfferCatalog'] = $defualt_Service['OfferCatalog'];
							if( !isset( $YourColor_Service['identifier'] ) || ( isset( $YourColor_Service['identifier'] ) && empty( $YourColor_Service['identifier'] ) ) ) $YourColor_Service['identifier'] = $defualt_Service['identifier'];
							if( !isset( $YourColor_Service['additionalType'] ) || ( isset( $YourColor_Service['additionalType'] ) && empty( $YourColor_Service['additionalType'] ) ) ) $YourColor_Service['additionalType'] = $defualt_Service['additionalType'];
							if( !isset( $YourColor_Service['ratingValue'] ) || ( isset( $YourColor_Service['ratingValue'] ) && empty( $YourColor_Service['ratingValue'] ) ) ) $YourColor_Service['ratingValue'] = $defualt_Service['ratingValue'];
							if( !isset( $YourColor_Service['reviewCount'] ) || ( isset( $YourColor_Service['reviewCount'] ) && empty( $YourColor_Service['reviewCount'] ) ) ) $YourColor_Service['reviewCount'] = $defualt_Service['reviewCount'];

							if( !empty( $thumbnail_url ) ){
				                $this->print_jsonld( array(
				                  	'@context' => 'http://schema.org',
				                  	'@type' => 'Service',
				                  	'serviceType' => $post->post_title,
				                  	'provider' => array(
					                    '@type' => 'LocalBusiness',
					                    'name' => $post->post_title,
					                    'url' => $Permalink,
					                    'priceRange' => $YourColor_Service['priceRange'],
					                    'image' => $thumbnail_url,
					                    'address' => array(
					                        '@type' => 'PostalAddress',
					                        'addressLocality' => $YourColor_Service['addressLocality'],
					                        'postalCode' => $YourColor_Service['postalCode'],
					                        'telephone' => $YourColor_Service['telephone'],
					                        'addressCountry' => $YourColor_Service['addressCountry'],
					                        'streetAddress' => $YourColor_Service['streetAddress'],
					                        'addressRegion' => $YourColor_Service['addressRegion'],
				                      	),
				                  	),
				                  	'areaServed' => array(
										'@type' => 'Place',
										'name' => $YourColor_Service['areaServed'],
				                  	),
				                  	'description' => $YourColor_Service['description'],
				                  	'url' => $Permalink,
				                  	'hasOfferCatalog' => array(
					                    '@type' => 'OfferCatalog',
					                    'name' => $YourColor_Service['OfferCatalog'],
				                    ),
				                  	'identifier' => $YourColor_Service['identifier'],
				                  	'additionalType' => $YourColor_Service['additionalType'],
				                ) );
							}
						}
					}
				# Article
	          		$hide_schema_Article = get_option('hide_schema_Article');
	        		if( empty( $hide_schema_Article ) ) {
						$YourColor_Article = get_post_meta( $post->ID,'YourColor_Article',true);
						$YourColor_Article = ( is_array( $YourColor_Article ) ) ? $YourColor_Article : array();
						#
						if( !isset( $YourColor_Article['hide_schema_Article'] ) || ( isset( $YourColor_Article['hide_schema_Article'] ) && empty( $YourColor_Article['hide_schema_Service'] ) ) ) {

							$defualt_Service = get_option('YourColor_Article');
							$defualt_Service = ( is_array( $defualt_Service ) ) ? $defualt_Service : array();
							if( !isset( $defualt_Service['headline'] ) || ( isset( $defualt_Service['headline'] ) && empty( $defualt_Service['headline'] ) ) ) $defualt_Service['headline'] = wp_trim_words( $post->post_content,20);
							if( !isset( $defualt_Service['description'] ) || ( isset( $defualt_Service['description'] ) && empty( $defualt_Service['description'] ) ) ) $defualt_Service['description'] = '';
							if( !isset( $defualt_Service['articleBody'] ) || ( isset( $defualt_Service['articleBody'] ) && empty( $defualt_Service['articleBody'] ) ) ) $defualt_Service['articleBody'] = '';

							#
							if( !isset( $YourColor_Article['description'] ) || ( isset( $YourColor_Article['description'] ) && empty( $YourColor_Article['description'] ) ) ) $YourColor_Article['description'] = $defualt_Service['description'];
							if( !isset( $YourColor_Article['headline'] ) || ( isset( $YourColor_Article['headline'] ) && empty( $YourColor_Article['headline'] ) ) ) $YourColor_Article['headline'] = $defualt_Service['headline'];
							if( !isset( $YourColor_Article['articleBody'] ) || ( isset( $YourColor_Article['articleBody'] ) && empty( $YourColor_Article['articleBody'] ) ) ) $YourColor_Article['articleBody'] = $defualt_Service['articleBody'];

							if( !empty( $thumbnail_url ) ){
						        $this->print_jsonld( array(
						          	'@context' => 'http://schema.org',
						          	'@type' => 'Article',
						          	'author' => array(
						                '@type' => 'Person',
						                'name' => $post_author->display_name,
						                'url' => $Author__url,
						          	),
						          	'headline' => $YourColor_Article['headline'],
						          	'image' => array( $thumbnail_url ),
						          	'datePublished' => date('c', strtotime( $publish_date) ),
						          	'dateModified' => date('c', strtotime( $modified_date) ),
						          	'publisher' => array(
					                    '@type' => 'Organization',
					                    'name' => $sitename__schema,
				                    	'logo' => array(
					                        '@type' => 'ImageObject',
					                        'url' => $logo__schema,
				                      	),
				                  	),
						          	'description' => $defualt_Service['description'],
						          	'articleBody' => $YourColor_Article['articleBody'],
						        ) );
							}
						}
					}
			 	## faqs
	          		$hide_schema_faqs = get_option('hide_schema_faqs');
	        		if( empty( $hide_schema_faqs ) ) {

					    $questionsMeta = get_post_meta($post->ID, 'yourcolor__faqs', true);
					    $questionsMeta = ( ( is_array( $questionsMeta ) ) ) ? $questionsMeta : array();
					    if( !empty( $questionsMeta ) ){

						    $questions = array();
						    if(!empty($questionsMeta)){
						        foreach( $questionsMeta as $q ) {
						            if( !empty($q['question']) ) {
						                $questions[] = $q;
						            }
						        }
						    }
						    $main_entity = array();
						    foreach ( $questions as $faq ) {
						    	$main_entity[] = array(
						    		'@type' => 'Question',
						    		'name' => isset( $faq['question'] ) ? $faq['question'] : '',
						    		'acceptedAnswer' => array(
						    			'@type' => 'Answer',
						    			'text' => isset( $faq['answer'] ) ? $faq['answer'] : '',
						    		),
						    	);
						    }
				            $this->print_jsonld( array(
				                '@context' => 'https://schema.org',
				                '@type' => 'FAQPage',
				                'mainEntity' => $main_entity,
				            ) );
					    }

					}
            	## Rating Schema
	          		$hide_schema_Rating = get_option('hide_schema_Rating');
	        		if( empty( $hide_schema_Rating ) ) {
						$YourColor__Rating = get_post_meta( $post->ID,'YourColor__Rating',true);
						$YourColor__Rating = ( is_array( $YourColor__Rating ) ) ? $YourColor__Rating : array();
						#
						if( !isset( $YourColor__Rating['hide_schema_rating'] ) || ( isset( $YourColor__Rating['hide_schema_rating'] ) && empty( $YourColor__Rating['hide_schema_rating'] ) ) ) {

							$defualt_Rating = get_option('YourColor_Rating');
							$defualt_Rating = ( is_array( $defualt_Rating ) ) ? $defualt_Rating : array();

							# # Get Option
							if( !isset( $defualt_Rating['RatingValue_def'] ) || ( isset( $defualt_Rating['RatingValue_def'] ) && empty( $defualt_Rating['RatingValue_def'] ) ) ) $defualt_Rating['RatingValue_def'] = '';
							#
							if( !isset( $defualt_Rating['Best_Rating_def'] ) || ( isset( $defualt_Rating['Best_Rating_def'] ) && empty( $defualt_Rating['Best_Rating_def'] ) ) ) $defualt_Rating['Best_Rating_def'] = '';
							#
							if( !isset( $defualt_Rating['RatingCount_def'] ) || ( isset( $defualt_Rating['RatingCount_def'] ) && empty( $defualt_Rating['RatingCount_def'] ) ) ) $defualt_Rating['RatingCount_def'] = '';

							# post meta

							if( !isset( $YourColor__Rating['Rating_Value'] ) || ( isset( $YourColor__Rating['Rating_Value'] ) && empty( $YourColor__Rating['Rating_Value'] ) ) ) $YourColor__Rating['Rating_Value'] = $defualt_Rating['RatingValue_def'];

							if( !isset( $YourColor__Rating['Best_Rating'] ) || ( isset( $YourColor__Rating['Best_Rating'] ) && empty( $YourColor__Rating['Best_Rating'] ) ) ) $YourColor__Rating['Best_Rating'] = $defualt_Rating['Best_Rating_def'];

							if( !isset( $YourColor__Rating['Rating_Count'] ) || ( isset( $YourColor__Rating['Rating_Count'] ) && empty( $YourColor__Rating['Rating_Count'] ) ) ) $YourColor__Rating['Rating_Count'] = $defualt_Rating['RatingCount_def'];


							if( !empty( $YourColor__Rating['Rating_Value'] ) ){
						        $this->print_jsonld( array(
						          	'@context' => 'http://schema.org',
						          	'@type' => 'CreativeWorkSeries',
								    'name' => $post->post_title,
								    'aggregateRating' => array(
								        '@type' => 'AggregateRating',
								        'ratingValue' => $YourColor__Rating['Rating_Value'],
								        'bestRating' => $YourColor__Rating['Best_Rating'],
								        'ratingCount' => $YourColor__Rating['Rating_Count'],
								    ),
						        ) );
							}
						}
					}
		}
	# ARCHIVE EDITS .
		public function Archive(){
			
		}

	# PAGES EDITS .
		public function Page(){
			
		}

	public function Author(){
	
	}
	# HOME EDITS .	
		public function Home(){
			

			$YourColor_Schema_business = get_option('YourColor_Schema_business');
			$YourColor_Schema_business = ( is_array( $YourColor_Schema_business ) ) ? $YourColor_Schema_business : array();

			$logo__schema = get_option('logo__schema');

			if( !isset( $YourColor_Schema_business['hide_schema_business'] ) || ( isset( $YourColor_Schema_business['hide_schema_business'] ) && empty( $YourColor_Schema_business['hide_schema_business'] ) ) ) {

		        $this->print_jsonld( array(
			          	'@context' => 'http://schema.org',
			          	'@type' => 'LocalBusiness',
			          	'name' => ( ( isset( $YourColor_Schema_business['Business_Name'] ) && !empty( $YourColor_Schema_business['Business_Name'] ) ) ? $YourColor_Schema_business['Business_Name'] : '' ),
			          	'description' => ( ( isset( $YourColor_Schema_business['description'] ) && !empty( $YourColor_Schema_business['description'] ) ) ? $YourColor_Schema_business['description'] : '' ),
			          	'address' => array(
				            '@type' => 'PostalAddress',
				            'streetAddress' => ( ( isset( $YourColor_Schema_business['Street_Address'] ) && !empty( $YourColor_Schema_business['Street_Address'] ) ) ? $YourColor_Schema_business['Street_Address'] : '' ),
				            'addressLocality' => ( ( isset( $YourColor_Schema_business['City'] ) && !empty( $YourColor_Schema_business['City'] ) ) ? $YourColor_Schema_business['City'] : '' ),
				            'addressRegion' => ( ( isset( $YourColor_Schema_business['State'] ) && !empty( $YourColor_Schema_business['State'] ) ) ? $YourColor_Schema_business['State'] : '' ),
				            'postalCode' => ( ( isset( $YourColor_Schema_business['Postal_Code'] ) && !empty( $YourColor_Schema_business['Postal_Code'] ) ) ? $YourColor_Schema_business['Postal_Code'] : '' ),
				            'addressCountry' => ( ( isset( $YourColor_Schema_business['Country'] ) && !empty( $YourColor_Schema_business['Country'] ) ) ? $YourColor_Schema_business['Country'] : '' ),
			          	),
			          	'telephone' => ( ( isset( $YourColor_Schema_business['telephone'] ) && !empty( $YourColor_Schema_business['telephone'] ) ) ? $YourColor_Schema_business['telephone'] : '' ),
			            'url' => home_url(),
			            'image' => ( ( !empty( $logo__schema ) ) ? $logo__schema : '' ),
			            'openingHours' => ( ( isset( $YourColor_Schema_business['openingHours'] ) && !empty( $YourColor_Schema_business['openingHours'] ) ) ? $YourColor_Schema_business['openingHours'] : '' ),
			            'priceRange' => ( ( isset( $YourColor_Schema_business['Price_Range'] ) && !empty( $YourColor_Schema_business['Price_Range'] ) ) ? $YourColor_Schema_business['Price_Range'] : '' ),
			          	'aggregateRating' => array(
				            '@type' => 'AggregateRating',
				            'ratingValue' => ( ( isset( $YourColor_Schema_business['ratingValue'] ) && !empty( $YourColor_Schema_business['ratingValue'] ) ) ? $YourColor_Schema_business['ratingValue'] : '' ),
				            'reviewCount' => ( ( isset( $YourColor_Schema_business['Rating_Count'] ) && !empty( $YourColor_Schema_business['Rating_Count'] ) ) ? $YourColor_Schema_business['Rating_Count'] : '' ),
			          	),
			        ) );

			}
			## Schema SearchAction
		 	$YourColor_Schema_websites = get_option('YourColor_Schema_websites');
		 	$YourColor_Schema_websites = ( is_array( $YourColor_Schema_websites ) ) ? $YourColor_Schema_websites : array();
		 	if( !isset( $YourColor_Schema_websites['hide_schema_websites'] ) || ( isset( $YourColor_Schema_websites['hide_schema_websites'] ) && empty( $YourColor_Schema_websites['hide_schema_websites'] ) ) ) {
            	$this->print_jsonld( array(
						'@context' => 'http://schema.org',
						'@type' => 'WebSite',
						'url' => home_url(),
						'potentialAction' => array(
							'@type' => 'SearchAction',
							'target' => home_url().'/?s={s}',
							'query-input' => 'required name=s',
						),
					) );
			}

		}

	public function insert__schema(){

		$validate__schema = get_option('validate__schema');

		if( !empty( $validate__schema ) ) return ;

		if( is_home() ) $this->Home();

		if( is_page() ) $this->Page();

		if( is_single() ) $this->Single();

		if( is_archive() || is_category() || is_tax() ) $this->Archive();

		if( is_author() ) $this->Author();
	}

	public function Setup(){
		# عند تعطيل KAYAN SEO (kayan_seo_disable): Rank Math يملك JSON-LD — لا نكرر Schema القالب.
		if ( function_exists( 'kayan_seo_is_disabled' ) && kayan_seo_is_disabled() ) {
			if ( function_exists( 'kayan_seo_rank_math_plugin_active' ) && kayan_seo_rank_math_plugin_active() ) {
				return;
			}
		}
		# الوضع الافتراضي: KAYAN SEO يعمل وواجهة Rank Math معطّلة → Schema القالب يُطبع.
		add_action('wp_head', array( $this,'insert__schema') );
	}
}
(new YourColor__Schema)->Setup();
