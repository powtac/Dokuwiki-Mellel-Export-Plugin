<?php

/*    
    <p style='ps-0' dir='ltr'>
        <c style='cs-0' lang='tl-0'>Test</c>
    </p>
    <p style='ps-0' dir='ltr'>
        <c style='cs-0' lang='tl-0' over='co-0'>Fett</c><c style='cs-0' lang='tl-0' over='co-1'/>
        <c style='cs-0' lang='tl-0' over='co-2'>Kursiv</c><c style='cs-0' lang='tl-0' over='co-1'/>
        <c style='cs-0' lang='tl-0' over='co-3'>Unterstrichen</c><c style='cs-0' lang='tl-0' over='co-4'> Freitext</c>
    </p>
    <p style='ps-0' dir='ltr'>
        <c style='cs-0' lang='tl-0' over='co-4'>Test String am Ende.</c>
        <c style='cs-0'>
            <selection-insertion-point/>
            <page-break/>
        </c>
    </p>
    <p style='ps-1' dir='ltr'>
        <c style='cs-0'/>
    </p>
</section>
*/



$m = array(
	// p = function name (either "p" or "p_open" and p_close)
	// replacement = The pattern to replace bzw. to split the template
	// template = template with replacement
	// alias = other common names for the tag
	// subpattern = when there are additional parts in the template which have to be replaced (ex. h1 and footnotes)
    
    
//<c style="cs-0">
    
    
	'p' => array(
		'replacement'	=> 'normaler Text',
		'alias'			=> array('section'),
		'subpattern'	=> array(),
        
        // Normaler Text mit Absatz!
        'template'      => "<p style='ps-0' dir='ltr'>normaler Text</p>",      
//        'template'      => "<c style='cs-0'>normaler Text</c>",
	),
	
    // normaler Text
	'cdata' => array(
		'replacement'	=> 'CDATA',
		'alias'			=> array(),
		'subpattern'	=> array(),
		'template'		=> "<c style='cs-0'>CDATA</c>",
	),
	
	'header' => array(
		'replacement'	=> 'UEBERSCHRIFT',
		'alias'			=> array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'),
		'subpattern'	=> array('{{LEVEL}}'),
		'template'		=> "<p style='ps-0' dir='ltr'>
    							<c style='cs-1'><autotitle level='{{LEVEL}}' index='0' model-string-length='{{LENGTH}}'><c style='cs-1' lang='tl-0'>UEBERSCHRIFT</c></autotitle></c>
						    </p>",
	),
	
	'footnote' => array(
		'replacement'	=> 'Fussnote',
		'alias'			=> array(),
		'subpattern'	=> array('{{NOTE}}'),
        
		// Template contains another template of "normaler Text"!
//		'template'		=> "<p style='ps-0' dir='ltr'>
//                                <c style='cs-3'>{{NOTE}}<note stream='nsm-0'>
//                                  <p style='ps-0' dir='ltr'>
//                                    <c style='cs-0' lang='tl-0'>Fussnote</c>
//                                  </p>
//                                </note>
//                              </c>
//                            </p>",
        'template'      => "<c style='cs-3'>{{NOTE}}<note stream='nsm-0'>
                              <p style='ps-0' dir='ltr'>
                                <c style='cs-0' lang='tl-0'>Fussnote</c>
                              </p>
                              </note>
                            </c>",
	),
    'externallink' => array(
        'replacement'   => 'EXTERNALLINK',
        'alias'         => array('internallink', 'internalmedia'), // TODO internalmedia might not work
        'subpattern'    => array('{{TITLE}}'),
        'template'      => "<c style='cs-0'><hyperlink display-as-link='yes'><display-text><c style='cs-0'>{{TITLE}}</c><c style='cs-0'/></display-text><url-string>EXTERNALLINK</url-string></hyperlink></c>",
    ),

	// http://mountaindragon.com/html/iso.htm
	'doublequoteopening' => array(
		'replacement'	=> 'DOUBLEQUOTEOPENING',
		'alias'			=> array(),
		'subpattern'	=> array(),
        #'template'      => '&qout;',
        'template'      => '<c style="cs-0">"</c>',
	),
	
	'doublequoteclosing' => array(
		'replacement'	=> 'DOUBLEQUOTECLOSING',
		'alias'			=> array(),
		'subpattern'	=> array(),
        #'template'      => '&qout;',
        'template'      => '<c style="cs-0">"</c>',
	),

	'singlequoteopening' => array(
		'replacement'	=> 'SINGLEQUOTEOPENING',
		'alias'			=> array(),
		'subpattern'	=> array(),
		'template'		=> '<c style="cs-0">\'</c>',
	),
	
	'singlequoteclosing' => array(
		'replacement'	=> 'SINGLEQUOTECLOSING',
		'alias'			=> array(),
		'subpattern'	=> array(),
		'template'        => '<c style="cs-0">\'</c>',
	),

	'plain' => array(
		'replacement'	=> 'PLAIN',
		'alias'			=> array('entity', 'acronym', 'preformatted'),
		'subpattern'	=> array(),
		'template'		=> "<c style='cs-0'>PLAIN</c>", // same template as "p", correct?
	),
	
    
//  <list style="ls-0">
//  <p style="ps-0" dir="ltr" list-level="1">
//    <c style="cs-0">Erstens </c>
//  </p>
//  <p style="ps-0" dir="ltr" list-level="1">
//    <c style="cs-0">Zweitens <selection-insertion-point/></c>
//  </p>
//  <p style="ps-0" dir="ltr" list-level="1">
//    <c style="cs-0">Drittens</c>
//  </p>
//  </list>
    
    'list' => array(
        'replacement'   => 'LIST',
        'alias'         => array('listu', 'listo'),
        'subpattern'    => array(),
        'template'      => '<list style="ls-0">LIST</list>',
        'template_p_open' => '</p><list style="ls-0">LIST</list><p style="ps-0" dir="ltr">',
    ),    
    
    'listcontent' => array(
        'replacement'   => 'LISTCONTENT',
        'alias'         => array(),
        'subpattern'    => array(),
        'template'      => 'LISTCONTENT',
    ),
    
    'listitem' => array(
        'replacement'   => 'LISTITEM',
        'alias'         => array(),
        'subpattern'    => array('{{LEVEL}}'),
        'template'      => '<p style="ps-0" dir="ltr" list-level="{{LEVEL}}">LISTITEM</p>',
    ),
    
	'strong' => array(
		'replacement'	=> 'BOLD',
		'alias'			=> array(),
		'subpattern'	=> array(),
		'template'		=> "<c style='cs-0' lang='tl-0' over='co-0'>BOLD</c>",
	),
	
	'emphasis' => array(
		'replacement'	=> 'EMPHASIS',
		'alias'			=> array('italic'),
		'subpattern'	=> array(),
		'template'		=> "<c style='cs-0' lang='tl-0' over='co-2'>EMPHASIS</c>",
	),
	
	'underline' => array(
		'replacement'	=> 'UNDERLINE',
		'alias'			=> array(),
		'subpattern'	=> array(),
		'template'		=> "<c style='cs-0' over='co-3'>UNDERLINE</c>",
	),
    
    'deleted' => array(
        'replacement'   => 'DELETED',
        'alias'         => array(),
        'subpattern'    => array(),
        'template'      => '<c style="cs-0" over="co-4">DELETED</c>',
    ),
	
    'monospace' => array(
        'replacement'   => 'MONOSPACE',
        'alias'         => array(),
        'subpattern'    => array(),
        'template'      => '<c style="cs-0" over="co-5">MONOSPACE</c>',
    ),
    
    'code' => array(
        'replacement'   => 'CODE',
        'alias'         => array(),
        'subpattern'    => array(),
        'template'      => '<p  style="ps-0" dir="ltr"><c style="cs-0" lang="tl-0" over="co-5">CODE</c></p>',
    ),
    
    'smiley' => array(
        'replacement'   => 'SMILEY',
        'alias'         => array(),
        'subpattern'    => array(),
        'template'      => 'SMILEY',
    ),
    
    'table' => array(
        'replacement'   => 'TABLE',
        'alias'         => array(),
        'subpattern'    => array(),
        'template'      => 'TABLE',
    ),
  
    'tablerow' => array(
        'replacement'   => 'TABLEROW',
        'alias'         => array(),
        'subpattern'    => array(),
        'template'      => "<p style='ps-0' dir='ltr'>TABLEROW</p>",
    ),
    
    'tablecell' => array(
        'replacement'   => 'TABLECELL',
        'alias'         => array(),
        'subpattern'    => array(),
        'template'      => '<c style="cs-0"> | </c>TABLECELL<c style="cs-0"> | </c>',
//        'template'      => 'TABLECELL ',
    ),
);