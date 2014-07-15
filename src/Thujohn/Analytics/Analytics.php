<?php namespace Thujohn\Analytics;

class Analytics {
	protected $client;
	protected $service;
	private $site_ids = array();

	public function __construct(\Google_Client $client) {
		$this->setClient($client);
		$this->setService($client);
	}

	public function getClient() {
		return $this->client;
	}

	public function setClient(\Google_Client $client) {
		$this->client = $client;

		return $this;
	}

	public function getService() {
		return $this->service;
	}

	public function setService(\Google_Client $client) {
		$this->service = new \Google_Service_Analytics($client);

		return $this;
	}

	public function query($id, $start_date, $end_date, $metrics, $others = array()) {
		return $this->service->data_ga->get($id, $start_date, $end_date, $metrics, $others);
	}

	public function segments() {
		return $this->service->management_segments;
	}

	public function accounts() {
		return $this->service->management_accounts;
	}

	public function goals() {
		return $this->service->management_goals;
	}

	public function profiles() {
		return $this->service->management_profiles;
	}

	public function webproperties() {
		return $this->service->management_webproperties;
	}

	public function getAllSitesIds() {
		if (empty($this->site_ids)) {
			$sites = $this->service->management_profiles->listManagementProfiles("~all", "~all");
			foreach($sites['items'] as $site) {
				$this->site_ids[] = array('id' => 'ga:' . $site['id'], 'url' => $site['websiteUrl']);
			}
		}

		return $this->site_ids;
	}

	public function getSiteIdByUrl($url) {
		if (!isset($this->site_ids[$url])) {
			$this->getAllSitesIds();
		}

		if (isset($this->site_ids[$url])) {
			return $this->site_ids[$url];
		}

		throw new \Exception("Site $url is not present in your Analytics account.");
	}
}