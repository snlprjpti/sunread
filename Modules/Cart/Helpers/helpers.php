<?php

function getCartHashIdFromHeader($request, $headerName):?string
{
    return is_array($request->header()["{$headerName}"]) ? $request->header()["{$headerName}"][array_key_first($request->header()["{$headerName}"])] : '';
}
