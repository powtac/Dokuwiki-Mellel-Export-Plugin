<?php



$m = array(
	// p = function name (either "p" or "p_open" and p_close)
	// replacement = The pattern to replace bzw. to split the template
	// template = template with replacement
	// alias = other common names for the tag
	// subpattern = when there are additional parts in the template which have to be replaced (ex. h1 and footnotes)
	'p' => array(
		'replacement'	=> 'normaler Text',
		'alias'			=> array('section'),
		'subpattern'	=> array(),
//		'template'		=> "<p style='ps-0' dir='ltr'>
//	                    <c style='cs-0' lang='tl-0'>normaler Text</c>
//	                </p>",
		'template'		=> "<p style='ps-3' dir='ltr'>
							<c style='cs-3' lang='tl-0'>normaler Text</c>
						</p>",
	),
	
	'cdata' => array(
		'replacement'	=> 'CDATA',
		'alias'			=> array(),
		'subpattern'	=> array(),
		'template'		=> 'CDATA',
	),
	
	'header' => array(
		'replacement'	=> 'UEBERSCHRIFT',
		'alias'			=> array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'),
		'subpattern'	=> array('{{LEVEL}}'),
//		'template'		=> "<p style='ps-0' dir='ltr'>
//	                <c style='cs-0'>
//	                  <autotitle level='{{LEVEL}}' index='0' >
//	                    <c style='cs-0'>Überschrift 1</c>
//	                  </autotitle>
//	                </c>
//	              </p>",
		#'template'		=> '<autotitle level="{{LEVEL}}" index="0">UEBERSCHRIFT</autotitle>',
		'template'		=> "<p style='ps-0' dir='ltr'>
							<c style='cs-0'>
								<autotitle level='{{LEVEL}}' index='0' model-string-length='30'>
									<c style='cs-0'>UEBERSCHRIFT</c>
								</autotitle>
							</c>
						</p>",
	),
	
	'footnote' => array(
		'replacement'	=> 'Fußnote',
		'alias'			=> array(),
		'subpattern'	=> array('{{NOTE}}'),
		// Template contains another template of "normaler Text"!
		'template'		=> "<p style='ps-3' dir='ltr'>
                    <c style='cs-3'>{{NOTE}}<note stream='nsm-0'>
                      <p style='ps-4' dir='ltr'>
                        <c style='cs-4' lang='tl-0'>Fußnote</c>
                      </p>
                    </note>
                  </c>
                </p>",
	),
	
	'internallink' => array(
		'replacement'	=> 'INTERNALLINK',
		'alias'			=> array('externallink'),
		'subpattern'	=> array('{{TITLE}}'),
		'template'		=> '[[INTERNALLINK ({{TITLE}})]]',
	),

	// http://mountaindragon.com/html/iso.htm
	'doublequoteopening' => array(
		'replacement'	=> 'DOUBLEQUOTEOPENING',
		'alias'			=> array(),
		'subpattern'	=> array(),
		'template'		=> 'DOUBLEQUOTEOPENING&#147;',
	),
	
	'doublequoteclosing' => array(
		'replacement'	=> 'DOUBLEQUOTECLOSING',
		'alias'			=> array(),
		'subpattern'	=> array(),
		'template'		=> '&#148;',
	),

	'singlequoteopening' => array(
		'replacement'	=> 'SINGLEQUOTEOPENING',
		'alias'			=> array(),
		'subpattern'	=> array(),
		'template'		=> '&#145;',
	),
	
	'singlequoteclosing' => array(
		'replacement'	=> 'SINGLEQUOTECLOSING',
		'alias'			=> array(),
		'subpattern'	=> array(),
		'template'		=> '&#146;',
	),

	'plain' => array(
		'replacement'	=> 'PLAIN',
		'alias'			=> array('entity', 'acronym', 'preformatted'),
		'subpattern'	=> array(),
		'template'		=> 'PLAIN',
	),
	
	'list' => array(
		'replacement'	=> 'LIST',
		'alias'			=> array('listcontent', 'listitem'),
		'subpattern'	=> array(),
		'template'		=> '',
	),
	
	'strong' => array(
		'replacement'	=> 'BOLD',
		'alias'			=> array(),
		'subpattern'	=> array(),
		'template'		=> "<p style='ps-3' dir='ltr'>
							<c style='cs-3' over='co-0'>BOLD</c>
						</p>",
	),
	
	'emphasis' => array(
		'replacement'	=> 'EMPHASIS',
		'alias'			=> array('italic'),
		'subpattern'	=> array(),
		'template'		=> '<c style="cs-3" over="co-1">EMPHASIS</c>
              <c style="cs-3" />',
	),
	
	'underline' => array(
		'replacement'	=> 'UNDERLINE',
		'alias'			=> array(),
		'subpattern'	=> array(),
		'template'		=> '<c style="cs-3" over="co-2">UNDERLINE</c>
              <c style="cs-3" />',
	),
	
	              
);