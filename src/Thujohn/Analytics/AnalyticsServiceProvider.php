<?php namespace Thujohn\Analytics;

use Illuminate\Support\ServiceProvider;

class AnalyticsServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('thujohn/analytics');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('Thujohn\Analytics\Analytics', function ($app) {
			if(!\File::exists($app['config']->get('analytics::certificate_path')))
			{
				throw new \Exception("Can't find the .p12 certificate in: " . $app['config']->get('analytics::certificate_path'));
			}

			$config = array(
				'oauth2_client_id' => $app['config']->get('analytics::client_id'),
				'use_objects' => $app['config']->get('analytics::use_objects'),
			);

			$client = new \Google_Client($config);

			$client->setAccessType('offline');

			$client->setAssertionCredentials(
				new \Google_Auth_AssertionCredentials(
					$app['config']->get('analytics::service_email'),
					array('https://www.googleapis.com/auth/analytics.readonly'),
					file_get_contents($app['config']->get('analytics::certificate_path'))
				)
			);

			return new Analytics($client);
		});

		$this->app->singleton('analytics', 'Thujohn\Analytics\Analytics');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('analytics');
	}

}