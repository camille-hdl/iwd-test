<?php

namespace IWD\JOBINTERVIEW\Repository;

interface RepositoryInterface
{
    /**
     * Retourne une liste d'éléments pour lesquels $predicate retoutne `true`
     *
     * @param \callable $predicate `function(array $element): boolean`
     * @return array
     */
    public function filter(callable $predicate): array;

    /**
     * Retourne tous les éléments
     *
     * @return array
     */
    public function all(): array;
}