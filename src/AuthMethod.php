<?php

namespace W0s1nsk1\TelescopeElasticsearchDriver;

enum AuthMethod: string
{
    case BASIC = 'basic';
    case API_KEY = 'api_key';
}