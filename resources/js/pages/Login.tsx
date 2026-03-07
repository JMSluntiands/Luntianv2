import { useState, useEffect } from 'react';
import { Mail, Lock, Eye, EyeOff, Square, Check, ArrowRight, Sun, Moon, Loader2 } from 'lucide-react';

const THEME_KEY = 'theme';

export default function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [rememberMe, setRememberMe] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [toastExiting, setToastExiting] = useState(false);
  const [isDark, setIsDark] = useState(() => {
    if (typeof window === 'undefined') return true;
    const saved = localStorage.getItem(THEME_KEY);
    return saved !== 'light';
  });

  useEffect(() => {
    document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
    localStorage.setItem(THEME_KEY, isDark ? 'dark' : 'light');
  }, [isDark]);

  const toggleTheme = () => setIsDark((prev) => !prev);

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
    // Demo: simulate API call then show error
    await new Promise((r) => setTimeout(r, 1500));
    setError('Invalid username or password. Please try again.');
    setIsLoading(false);
  };

  return (
    <div className={`min-h-screen font-sans flex flex-col items-center justify-center p-6 relative overflow-x-hidden ${isDark ? 'bg-[#1A2534]' : 'bg-gray-100'}`}>
      {/* Top right: theme toggle + toast */}
      <div className="absolute top-6 right-6 flex flex-col items-end gap-3 z-50">
        <button
          type="button"
          onClick={toggleTheme}
          className={`p-2 rounded-lg transition-colors cursor-pointer ${isDark ? 'text-white hover:bg-white/10' : 'text-gray-800 hover:bg-gray-200'}`}
          aria-label="Toggle theme"
        >
          {isDark ? <Sun className="w-6 h-6" /> : <Moon className="w-6 h-6" />}
        </button>
        {error && (
          <div
            role="alert"
            onClick={dismissToast}
            className={`bg-white rounded-xl shadow-lg p-4 max-w-[271px] flex items-center gap-3 cursor-pointer hover:shadow-xl transition-shadow ${toastExiting ? 'animate-toast-out' : 'animate-toast-in'}`}
          >
            <Mail className="w-5 h-5 text-[#0E1D2D] shrink-0" />
            <div className="flex-1 min-w-0">
              <p className="font-bold text-[#0E1D2D]">Failed</p>
              <p className="text-sm text-[#213448] font-normal break-words">{error}</p>
            </div>
          </div>
        )}
      </div>

      <div className="w-full max-w-md">
        {/* Logo */}
        <p className={`text-2xl font-bold uppercase text-center mb-10 ${isDark ? 'text-white' : 'text-gray-800'}`}>
          LOGO HERE
        </p>

        {/* Form */}
        <div className="space-y-6">
          <div>
            <h1 className={`text-2xl font-bold ${isDark ? 'text-white' : 'text-gray-800'}`}>Sign In</h1>
            <p className={`text-sm mt-1 ${isDark ? 'text-[#A0AEC0]' : 'text-gray-600'}`}>Login your credentials.</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4">
            {/* Email */}
            <div className="relative">
              <Mail className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#2C528B]" />
              <input
                type="email"
                placeholder="admin@example.com"
                autoComplete='off'
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="w-full pl-14 pr-5 py-4 rounded-xl bg-white border border-gray-200 text-[#2D3748] placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-[#2C528B]/30 focus:border-[#2C528B]"
              />
            </div>

            {/* Password */}
            <div className="relative">
              <Lock className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#2C528B]" />
              <input
                type={showPassword ? 'text' : 'password'}
                placeholder="••••••••••"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                className="w-full pl-14 pr-14 py-4 rounded-xl bg-white border border-gray-200 text-[#2D3748] placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-[#2C528B]/30 focus:border-[#2C528B]"
              />
              <button
                type="button"
                onClick={() => setShowPassword(!showPassword)}
                className="absolute right-4 top-1/2 -translate-y-1/2 text-[#2C528B] hover:opacity-80 cursor-pointer"
                aria-label={showPassword ? 'Hide password' : 'Show password'}
              >
                {showPassword ? (
                  <EyeOff className="w-5 h-5" />
                ) : (
                  <Eye className="w-5 h-5" />
                )}
              </button>
            </div>

            {/* Remember me */}
            <label className="flex items-center gap-2 cursor-pointer select-none">
              <span className={`flex items-center justify-center w-5 h-5 ${isDark ? 'text-white/70' : 'text-gray-600'}`}>
                {rememberMe ? (
                  <Check className="w-5 h-5 text-[#2C528B]" strokeWidth={2.5} />
                ) : (
                  <Square className="w-5 h-5" strokeWidth={2} />
                )}
              </span>
              <input
                type="checkbox"
                checked={rememberMe}
                onChange={(e) => setRememberMe(e.target.checked)}
                className="sr-only"
              />
              <span className={`text-sm font-normal ${isDark ? 'text-white' : 'text-gray-800'}`}>Remember me</span>
            </label>

            {/* Sign In button */}
            <button
              type="submit"
              disabled={isLoading}
              className="w-full flex items-center justify-center gap-2 py-3 rounded-xl bg-[#2C528B] text-white font-bold hover:bg-[#234a77] disabled:opacity-90 disabled:cursor-not-allowed cursor-pointer transition-all focus:outline-none focus:ring-2 focus:ring-[#2C528B] focus:ring-offset-2 focus:ring-offset-[#1A2534]"
            >
              {isLoading ? (
                <>
                  <Loader2 className="w-5 h-5 animate-spin" />
                  <span>Signing in...</span>
                </>
              ) : (
                <>
                  Sign In
                  <ArrowRight className="w-5 h-5" />
                </>
              )}
            </button>
          </form>
        </div>
      </div>
    </div>
  );
}
