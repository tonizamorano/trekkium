<?php
if ( ! defined( 'ABSPATH' ) ) exit;

return '

/* PLANTILLA BASE */

body { 
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
    margin:0; 
    padding:0; 
}

.mail-container { 
    max-width:600px; 
    margin:0 auto; 
    background: #fff;
    border: 2px solid #0b568b;
    border-radius: 10px;
}

.mail-header { 
    background: #0b568b;
    padding:15px; 
    text-align:center; 
    border-radius: 8px 8px 0 0;
    border: 2px solid #0b568b;
}

.mail-header img { 
    max-width:200px; 
}

.mail-content { 
    padding: 20px; 
    font-size:16px;
    color: #0b568b;
}

.mail-footer { 
    background: #0b568b;
    padding: 15px; 
    font-size: 14px; 
    color: #fff; 
    text-align:center; 
    border-radius: 0 0 8px 8px;
}

.mail-footer-contacto { 
    color: #fff; 
}

.mail-footer img { 
    max-width:150px; 
}

/* Reset enlaces automáticos */
a { text-decoration:none !important; }

a[x-apple-data-detectors],
span.MsoHyperlink,
u + #body a {
    color: inherit !important;
    text-decoration: none !important;
    font-family: inherit !important;
}


/* E-MAILS */

.mail-content-contenedor { 
    

}

.mail-content-seccion { 
    margin-bottom:20px; 
}

.mail-content-seccion p { 
    margin: 0px !important;
    line-height: 1.2;
}

.mail-content-seccion h4 { 
    margin-bottom: 10px !important;
    line-height: 1;
    font-size: 18px;
}


.mail-button { 
    display:inline-block; 
    padding:10px 15px; 
    background:#E67E22; 
    color:#fff !important;
    text-decoration:none !important;
    border-radius:50px; 
    margin-top: 15px; 
    line-height: 1;
    font-weight: 500;
}

.mail-button:hover { 
    background:#f5cba7; 
    color:#fff !important;
}

';
