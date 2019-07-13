<?php

// From CLI installer:

	private $optionMap = [
		'dbtype' => 'wgDBtype',
		'dbserver' => 'wgDBserver',
		'dbname' => 'wgDBname',
		'dbuser' => 'wgDBuser',
		'dbpass' => 'wgDBpassword',
		'dbprefix' => 'wgDBprefix',
		'dbtableoptions' => 'wgDBTableOptions',
		'dbport' => 'wgDBport',
		'dbschema' => 'wgDBmwschema',
->		'dbpath' => 'wgSQLiteDataDir',
		'server' => 'wgServer',
		'scriptpath' => 'wgScriptPath',
	];


// From core 
		if ( isset( $params['factory'] ) ) {
			return call_user_func( $params['factory'], $params );
		} elseif ( isset( $params['class'] ) ) {
			$class = $params['class'];
			// Automatically set the 'async' update handler
			$params['asyncHandler'] = $params['asyncHandler'] ?? 'DeferredUpdates::addCallableUpdate';
			// Enable reportDupes by default
			$params['reportDupes'] = $params['reportDupes'] ?? true;
			// Do b/c logic for SqlBagOStuff
			if ( is_a( $class, SqlBagOStuff::class, true ) ) {
				if ( isset( $params['server'] ) && !isset( $params['servers'] ) ) {
					$params['servers'] = [ $params['server'] ];
					unset( $params['server'] );
				}
				// In the past it was not required to set 'dbDirectory' in $wgObjectCaches
				if ( isset( $params['servers'] ) ) {
					foreach ( $params['servers'] as &$server ) {
						if ( $server['type'] === 'sqlite' && !isset( $server['dbDirectory'] ) ) {
							$server['dbDirectory'] = MediaWikiServices::getInstance()
->								->getMainConfig()->get( 'SQLiteDataDir' );
						}
					}
				}
			}

// MWLBFactory

	public static $applyDefaultConfigOptions = [
		'DBcompress',
		'DBDefaultGroup',
		'DBmwschema',
		'DBname',
		'DBpassword',
		'DBport',
		'DBprefix',
		'DBserver',
		'DBservers',
		'DBssl',
		'DBtype',
		'DBuser',
		'DBWindowsAuthentication',
		'DebugDumpSql',
		'DebugLogFile',
		'ExternalServers',
->		'SQLiteDataDir',
		'SQLMode',
	];

	private static function initServerInfo( array $server, ServiceOptions $options ) {
		if ( $server['type'] === 'sqlite' ) {
			$httpMethod = $_SERVER['REQUEST_METHOD'] ?? null;
			// T93097: hint for how file-based databases (e.g. sqlite) should go about locking.
			// See https://www.sqlite.org/lang_transaction.html
			// See https://www.sqlite.org/lockingv3.html#shared_lock
			$isHttpRead = in_array( $httpMethod, [ 'GET', 'HEAD', 'OPTIONS', 'TRACE' ] );
			$server += [
=>				'dbDirectory' => $options->get( 'SQLiteDataDir' ),
				'trxMode' => $isHttpRead ? 'DEFERRED' : 'IMMEDIATE'
			];
			/**
			 * When SQLite indexes were introduced in r45764, it was noted that
			 * SQLite requires index names to be unique within the whole database,
			 * not just within a schema. As discussed in CR r45819, to avoid the
			 * need for a schema change on existing installations, the indexes
			 * were implicitly mapped from the new names to the old names.
			 *
			 * This mapping can be removed if DB patches are introduced to alter
			 * the relevant tables in existing installations. Note that because
			 * this index mapping applies to table creation, even new installations
			 * of MySQL have the old names (except for installations created during
			 * a period where this mapping was inappropriately removed, see
			 * T154872).
			 */

// SearchEngineFactory.php
	public static function getSearchEngineClass( $dbOrLb ) {
		$type = ( $dbOrLb instanceof IDatabase )
			? $dbOrLb->getType()
			: $dbOrLb->getServerType( $dbOrLb->getWriterIndex() );

		switch ( $type ) {
=>			case 'sqlite':
=>				return SearchSqlite::class;
			case 'mysql':
				return SearchMySQL::class;
			case 'postgres':
				return SearchPostgres::class;
			case 'mssql':
				return SearchMssql::class;
			case 'oracle':
				return SearchOracle::class;
			default:
				return SearchEngineDummy::class;
		}
	}

// ProfileOutputDv
	public function log( array $stats ) {
		try {
			$dbw = wfGetDB( DB_MASTER );
		} catch ( DBError $e ) {
			return; // ignore
		}

		$fname = __METHOD__;
		$dbw->onTransactionCommitOrIdle( function () use ( $stats, $fname, $dbw ) {
			$pfhost = $this->perHost ? wfHostname() : '';
			// Sqlite: avoid excess b-tree rebuilds (mostly for non-WAL mode)
			// non-Sqlite: lower contention with small transactions
=>			$useTrx = ( $dbw->getType() === 'sqlite' );

			try {
=>				$useTrx ? $dbw->startAtomic( $fname ) : null;

// ApiQueryAllPages.php

		} elseif ( $params['filterlanglinks'] == 'withlanglinks' ) {
			$this->addTables( 'langlinks' );
			$this->addWhere( 'page_id=ll_from' );
			$this->addOption( 'STRAIGHT_JOIN' );

			// MySQL filesorts if we use a GROUP BY that works with the rules
			// in the 1992 SQL standard (it doesn't like having the
			// constant-in-WHERE page_namespace column in there). Using the
			// 1999 rules works fine, but that breaks other DBs. Sigh.
			/// @todo Once we drop support for 1992-rule DBs, we can simplify this.
			$dbType = $db->getType();
=>			if ( $dbType === 'mysql' || $dbType === 'sqlite' ) {
				// Ignore the rules, or 1999 rules if you count unique keys
				// over non-NULL columns as satisfying the requirement for
				// "functional dependency" and don't require including
				// constant-in-WHERE columns in the GROUP BY.
				$this->addOption( 'GROUP BY', [ 'page_title' ] );
