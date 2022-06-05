<?php

namespace p1\view\routing;

interface EndpointResolver {
  function resolve(string $request): bool;
}