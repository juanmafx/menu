<?php

declare(strict_types=1);

use Menu\Navigation\Page;

return [
    new Page('/', 'Home', 'Dynamic Menu Example', 'Landing page of the dynamic menu example.', 'home'),
    new Page('/about', 'About', 'About', 'Explains how the shared menu is composed.', 'about'),
    new Page('/contact', 'Contact', 'Contact', 'Placeholder for public contact details.', 'contact'),
];
