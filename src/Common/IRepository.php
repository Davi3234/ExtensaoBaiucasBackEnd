<?php

namespace App\Common;

use App\Provider\Sql\DeleteSQLBuilder;
use App\Provider\Sql\InsertSQLBuilder;
use App\Provider\Sql\SelectSQLBuilder;
use App\Provider\Sql\UpdateSQLBuilder;

interface IRepository {
  function create(InsertSQLBuilder $insertBuilder);
  function update(UpdateSQLBuilder $updateBuilder);
  function delete(DeleteSQLBuilder $deleteBuilder);
  function checkExistsOrTrow(SelectSQLBuilder $selectBuilder);
  function isExists(SelectSQLBuilder $selectBuilder);
  function findFirstOrThrow(SelectSQLBuilder $selectBuilder);
  function findFirst(SelectSQLBuilder $selectBuilder);
  function findUniqueOrThrow(SelectSQLBuilder $selectBuilder);
  function findUnique(SelectSQLBuilder $selectBuilder);
  function query(SelectSQLBuilder $selectBuilder);
  function findMany(SelectSQLBuilder $selectBuilder);
  function count(SelectSQLBuilder $selectBuilder);
}
