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

    /**
     * Runs analytics query calls in batch mode
     * It accepts an array of queries as specified by the parameters of the Analytics::query function
     * With an additional optional parameter named key, which is used to identify the results for a specific object
     *
     * Returns an array with object keys as response-KEY where KEY is the key you specified or a random key returned
     * from analytics.
     * @param array $queries
     * @return array|null
     */
    public function batchQueries(array $queries) {

        /*
         * Set the client to use batch mode
         * When batch mode is activated calls to Analytics::query will return
         * the request object instead of the resulting data
         */
        $this->client->setUseBatch(true);

        $batch = new \Google_Http_Batch($this->client);
        foreach ($queries as $query) {

            // pull the key from the array if specified so we can later identify our result
            $key = array_pull($query, 'key');

            // call the original query method to get the request object
            $req = call_user_func_array(__NAMESPACE__ .'\Analytics::query' ,$query);

            $batch->add($req, $key);
        }

        $results = $batch->execute();

        // Set the client back to normal mode
        $this->client->setUseBatch(false);

        return $results;
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
				$this->site_ids[$site['websiteUrl']] = 'ga:' . $site['id'];
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