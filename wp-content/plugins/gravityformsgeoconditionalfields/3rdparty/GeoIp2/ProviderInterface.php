<?php

namespace gfgcfGeoIp2;

interface ProviderInterface
{
    /**
     * @param ipAddress
     *            IPv4 or IPv6 address to lookup.
     * @return \gfgcfGeoIp2\Model\Country A Country model for the requested IP address.
     */
    public function country($ipAddress);

    /**
     * @param ipAddress
     *            IPv4 or IPv6 address to lookup.
     * @return \gfgcfGeoIp2\Model\City A City model for the requested IP address.
     */
    public function city($ipAddress);

    /**
     * @param ipAddress
     *            IPv4 or IPv6 address to lookup.
     * @return \gfgcfGeoIp2\Model\CityIspOrg A CityIspOrg model for the requested IP address.
     */
    public function cityIspOrg($ipAddress);

    /**
     * @param ipAddress
     *            IPv4 or IPv6 address to lookup.
     * @return \gfgcfGeoIp2\Model\Omni An Omni model for the requested IP address.
     */
    public function omni($ipAddress);
}
