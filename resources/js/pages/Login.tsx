import { useState, useEffect, useMemo } from 'react';
import { Mail, Lock, Eye, EyeOff, Check, ArrowRight, Sun, Moon, Loader2, CheckCircle2 } from 'lucide-react';

const THEME_KEY = 'theme';

export default function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [rememberMe, setRememberMe] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [toastExiting, setToastExiting] = useState(false);
  const [loginSuccess, setLoginSuccess] = useState(false);
  const [redirectUrl, setRedirectUrl] = useState<string | null>(null);
  const [isDark, setIsDark] = useState(() => {
    if (typeof window === 'undefined') return true;
    const saved = localStorage.getItem(THEME_KEY);
    return saved !== 'light';
  });
  const [iconAnimating, setIconAnimating] = useState(false);

  const logoUrl = useMemo(() => {
    if (typeof document === 'undefined') return '/storage/logo-light.png';
    return document.querySelector('meta[name="logo-url"]')?.getAttribute('content') || '/storage/logo-light.png';
  }, []);

  useEffect(() => {
    document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
    localStorage.setItem(THEME_KEY, isDark ? 'dark' : 'light');
  }, [isDark]);

  const toggleTheme = () => {
    setIconAnimating(true);
    setIsDark((prev) => !prev);
    setTimeout(() => setIconAnimating(false), 300);
  };

  const dismissToast = () => {
    setToastExiting(true);
    setTimeout(() => {
      setError(null);
      setToastExiting(false);
    }, 250);
  };

  useEffect(() => {
    if (!error) return;
    const t = setTimeout(() => dismissToast(), 4000);
    return () => clearTimeout(t);
  }, [error]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (isLoading) return;
    setIsLoading(true);
    setError(null);

    const token = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content;
    const formData = new FormData();
    formData.append('_token', token || '');
    formData.append('login', email.trim());
    formData.append('password', password);
    formData.append('remember', rememberMe ? '1' : '0');

    try {
      const res = await fetch('/login', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        credentials: 'same-origin',
      });
      const data = await res.json().catch(() => ({}));
      if (res.ok && data.success && data.redirect) {
        setRedirectUrl(data.redirect);
        setLoginSuccess(true);
        setIsLoading(false);
        setTimeout(() => {
          window.location.href = data.redirect;
        }, 2200);
        return;
      }
      setError(data.message || 'Invalid username or password. Please try again.');
    } catch {
      setError('Invalid username or password. Please try again.');
    } finally {
      setIsLoading(false);
    }
  };

  const isDarkClasses = {
    bg: isDark ? 'bg-slate-900' : 'bg-emerald-50/80',
    card: isDark ? 'bg-slate-800/90 border-slate-700 shadow-xl shadow-black/20' : 'bg-white border-slate-200 shadow-lg shadow-slate-200/50',
    heading: isDark ? 'text-white' : 'text-slate-800',
    subtext: isDark ? 'text-slate-400' : 'text-slate-500',
    input: isDark
      ? 'bg-slate-700/50 border-slate-600 text-white placeholder:text-slate-400 focus:ring-emerald-500/50 focus:border-emerald-500'
      : 'bg-white border-slate-200 text-slate-800 placeholder:text-slate-400 focus:ring-emerald-500/30 focus:border-emerald-500',
    icon: isDark ? 'text-emerald-400' : 'text-emerald-600',
    checkbox: isDark ? 'text-slate-300' : 'text-slate-600',
    toggle: isDark ? 'bg-slate-700/80 hover:bg-slate-600 text-emerald-400 focus:ring-emerald-500/50' : 'bg-white hover:bg-slate-50 text-emerald-600 focus:ring-emerald-500/50 border border-slate-200',
  };

  return (
    <div className={`min-h-screen font-sans flex flex-col items-center justify-center p-4 sm:p-6 relative overflow-x-hidden transition-colors duration-300 ${isDarkClasses.bg}`}>
      {/* Login success overlay – animation before redirect to dashboard */}
      {loginSuccess && (
        <div
          className={`login-success-overlay ${!isDark ? 'login-success-overlay-light' : ''}`}
          role="status"
          aria-live="polite"
          aria-label="Login successful, redirecting to dashboard"
        >
          <div className="login-success-card">
            <div className="login-success-icon-wrap">
              <CheckCircle2 className="login-success-icon" aria-hidden />
            </div>
            <h2 className={`text-xl sm:text-2xl font-bold mt-6 ${isDark ? 'text-white' : 'text-slate-800'}`}>
              Welcome back!
            </h2>
            <p className={`text-sm mt-2 ${isDark ? 'text-slate-400' : 'text-slate-500'}`}>
              Taking you to the dashboard...
            </p>
            <div className="login-success-progress" />
          </div>
        </div>
      )}

      {/* Architectural background animation */}
      <div
        className={`login-bg-arch ${isDark ? 'login-bg-arch-dark' : 'login-bg-arch-light'}`}
        aria-hidden
      >
        <div className="login-bg-arch-gradient" />
      </div>
      {/* Top right: theme toggle + toast */}
      <div className="absolute top-4 right-4 sm:top-6 sm:right-6 flex flex-col items-end gap-3 z-50">
        <button
          type="button"
          onClick={toggleTheme}
          disabled={iconAnimating}
          className={`relative flex items-center justify-center w-10 h-10 sm:w-11 sm:h-11 rounded-xl cursor-pointer transition-all duration-300 ease-out active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-transparent ${isDarkClasses.toggle}`}
          aria-label={isDark ? 'Switch to light mode' : 'Switch to dark mode'}
        >
          <span className={`inline-flex items-center justify-center ${iconAnimating ? 'theme-icon-enter' : ''}`}>
            {isDark ? <Sun className="w-5 h-5" strokeWidth={2} /> : <Moon className="w-5 h-5" strokeWidth={2} />}
          </span>
        </button>
        {error && (
          <div
            role="alert"
            onClick={dismissToast}
            className={`bg-white rounded-xl shadow-lg p-4 max-w-[280px] flex items-center gap-3 cursor-pointer hover:shadow-xl transition-shadow border border-red-100 text-left ${toastExiting ? 'animate-toast-out' : 'animate-toast-in'}`}
          >
            <div className="flex-shrink-0 w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center">
              <Mail className="w-4 h-4 text-red-600" />
            </div>
            <div className="flex-1 min-w-0">
              <p className="font-semibold text-slate-800">Login failed</p>
              <p className="text-sm text-slate-600 font-normal break-words mt-0.5">{error}</p>
            </div>
          </div>
        )}
      </div>

      <div className="w-full max-w-[400px] relative z-10">
        {/* Card container – matches logo branding */}
        <div className={`rounded-2xl border px-6 sm:px-8 py-8 sm:py-10 transition-colors duration-300 ${isDarkClasses.card}`}>
          {/* Logo – Luntian branding */}
          <div className="flex justify-center mb-8">
            <img
              src={logoUrl}
              alt="Luntian"
              className="max-h-14 sm:max-h-16 w-auto object-contain"
              onError={(e) => {
                const target = e.currentTarget;
                target.style.display = 'none';
                const fallback = target.nextElementSibling as HTMLElement;
                if (fallback) fallback.style.display = 'block';
              }}
            />
            <p
              className={`text-2xl sm:text-3xl font-bold uppercase tracking-wide text-center w-full text-emerald-600 ${isDark ? '!text-emerald-400' : ''}`}
              style={{ display: 'none' }}
              aria-hidden="true"
            >
              LUNTIAN
            </p>
          </div>

          <div className="space-y-6">
            <div className="text-center sm:text-left">
              <h1 className={`text-xl sm:text-2xl font-bold ${isDarkClasses.heading}`}>Welcome back</h1>
              <p className={`text-sm mt-1 ${isDarkClasses.subtext}`}>Sign in with your username or email.</p>
            </div>

            <form onSubmit={handleSubmit} className="space-y-4" autoComplete="off">
              <div>
                <label htmlFor="login-username" className="sr-only">Username or email</label>
                <div className="relative">
                  <Mail className={`absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 ${isDarkClasses.icon}`} aria-hidden />
                  <input
                    id="login-username"
                    type="text"
                    placeholder="Username or email"
                    autoComplete="username"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className={`w-full pl-12 pr-4 py-3.5 rounded-xl border text-base transition-colors focus:outline-none focus:ring-2 ${isDarkClasses.input}`}
                  />
                </div>
              </div>

              <div>
                <label htmlFor="login-password" className="sr-only">Password</label>
                <div className="relative">
                  <Lock className={`absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 ${isDarkClasses.icon}`} aria-hidden />
                  <input
                    id="login-password"
                    type={showPassword ? 'text' : 'password'}
                    placeholder="Password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    autoComplete="current-password"
                    className={`w-full pl-12 pr-12 py-3.5 rounded-xl border text-base transition-colors focus:outline-none focus:ring-2 ${isDarkClasses.input}`}
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className={`absolute right-4 top-1/2 -translate-y-1/2 p-1 rounded-lg hover:bg-black/5 transition-colors cursor-pointer ${isDarkClasses.icon}`}
                    aria-label={showPassword ? 'Hide password' : 'Show password'}
                  >
                    {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                  </button>
                </div>
              </div>

              <label className="flex items-center gap-3 cursor-pointer select-none group">
                <span className={`flex items-center justify-center w-5 h-5 rounded border-2 transition-colors ${rememberMe ? 'bg-emerald-500 border-emerald-500 text-white' : isDark ? 'border-slate-500 group-hover:border-slate-400' : 'border-slate-300 group-hover:border-slate-400'}`}>
                  {rememberMe && <Check className="w-3.5 h-3.5" strokeWidth={2.5} />}
                </span>
                <input
                  type="checkbox"
                  checked={rememberMe}
                  onChange={(e) => setRememberMe(e.target.checked)}
                  className="sr-only"
                />
                <span className={`text-sm ${isDarkClasses.checkbox}`}>Remember me</span>
              </label>

              <button
                type="submit"
                disabled={isLoading}
                className="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-500 active:bg-emerald-700 disabled:opacity-70 disabled:cursor-not-allowed cursor-pointer transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-transparent"
              >
                {isLoading ? (
                  <>
                    <Loader2 className="w-5 h-5 animate-spin" aria-hidden />
                    <span>Signing in...</span>
                  </>
                ) : (
                  <>
                    Sign in
                    <ArrowRight className="w-5 h-5" aria-hidden />
                  </>
                )}
              </button>
            </form>
          </div>
        </div>

        <p className={`text-center text-sm mt-6 ${isDarkClasses.subtext}`}>
          Use your Luntian account to access the dashboard.
        </p>
      </div>
    </div>
  );
}
