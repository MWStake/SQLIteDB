<?php #-*-tab-width: 4; fill-column: 76; indent-tabs-mode: t -*-
# vi:shiftwidth=4 tabstop=4 textwidth=76

/**
 * Sqlite-specific installer.
 *
 * Copyright (C) 2019  NicheWork, LLC
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace MediaWiki\Extension\SQLiteDB;

use Wikimedia\Rdbms\Field;

class SQLiteField implements Field {
	private $info, $tableName;

	function __construct( $info, $tableName ) {
		$this->info = $info;
		$this->tableName = $tableName;
	}

	function name() {
		return $this->info->name;
	}

	function tableName() {
		return $this->tableName;
	}

	function defaultValue() {
		if ( is_string( $this->info->dflt_value ) ) {
			// Typically quoted
			if ( preg_match( '/^\'(.*)\'$/', $this->info->dflt_value, $matches ) ) {
				return str_replace( "''", "'", $matches[1] );
			}
		}

		return $this->info->dflt_value;
	}

	/**
	 * @return bool
	 */
	function isNullable() {
		return !$this->info->notnull;
	}

	function type() {
		return $this->info->type;
	}
}
