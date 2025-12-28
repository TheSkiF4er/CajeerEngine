<?php
return [
  'guest' => ['content.read'],
  'editor' => ['content.read','content.write','templates.read','templates.write'],
  'seo' => ['content.read','seo.write'],
  'admin' => ['*'],
];
