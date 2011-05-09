Name:           php-slurm
Version:        1.0
Release:        6%{?dist}
Summary:        PHP extension for SLURM

Group:          System
License:        GPLv2
#URL:           TBD
Source0:        php-slurm-1.0.tar.bz2
BuildRoot:      %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)

BuildRequires: slurm-devel >= 2.2.0, php-devel, php-cli
Requires:      slurm >= 2.2.0, php, httpd

%description
PHP extension for SLURM.

This is PHP extensions goal is to provide just enough functionality to
a web developer read data from the slurm controller daemon to create
a *status* or *monitoring* application which can be viewed by the
end user. All the code has been written by 'Vermeulen Peter' with
contributions from TCHPC staff.


%prep
%setup -q


%build
phpize
%configure
make %{?_smp_mflags}


%install
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT
INSTALL_ROOT=$RPM_BUILD_ROOT make install 
install -D etc/php.d/slurm_php.ini $RPM_BUILD_ROOT/etc/php.d/slurm_php.ini
install -D etc/httpd/conf.d/slurm_php.conf $RPM_BUILD_ROOT/etc/httpd/conf.d/slurm_php.conf

%{__mkdir} -p %{buildroot}/%{_datadir}/%{name}
%{__cp} -ad ./examples/* %{buildroot}/%{_datadir}/%{name}


%clean
rm -rf $RPM_BUILD_ROOT


%files
%defattr(-,root,root,-)
%doc README AUTHORS RELEASE_NOTES
%{_libdir}/*
%config(noreplace) %{_sysconfdir}/httpd/conf.d/slurm_php.conf
%config(noreplace) %{_sysconfdir}/php.d/slurm_php.ini
%{_datadir}/%{name}


%changelog
* Tue Apr 12 2011 Jimmy Tang <jtang@tchpc.tcd.ie> - 1.0-6
Update example site to show functionality of module

* Mon Apr 04 2011 Jimmy Tang <jtang@tchpc.tcd.ie> - 1.0-5
Re-organise sample site

* Mon Apr 04 2011 Jimmy Tang <jtang@tchpc.tcd.ie> - 1.0-4
Added example configuration and sample site

* Mon Apr 04 2011 Jimmy Tang <jtang@tchpc.tcd.ie> - 1.0-3
Enable module when rpm is installed

* Fri Apr 01 2011 Jimmy Tang <jtang@tchpc.tcd.ie> - 1.0-2
Initial creation of spec file

